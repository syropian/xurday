<?php

namespace XurDay\Lib;

use XurDay\Exceptions\XurNotPresentException;
use XurDay\Lib\HTTPClient;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class XurClient {

  /**
   * @var HTTPClient
   */
  protected $client;

  /**
   * @param string $key
   */
  public function __construct($key) {
    $this->client = new HTTPClient(
      'https://www.bungie.net/Platform/Destiny/',
      ['X-API-Key' => $key]
    );
  }

  /**
   * @return array
   */
  public function getInventory() {
    $inventory = [];
    $inventoryHash = $this->client->getJSON('Advisors/Xur/');

    if ($inventoryHash['ErrorStatus'] == 'DestinyVendorNotFound') {
      throw new XurNotPresentException();
    }

    $itemHashes = $this->getItemHashes($inventoryHash);
    foreach($itemHashes as $hashId) {
      $inventory[] = $this->decodeItemHash($hashId);
    }
    $uniqueInventory = array_map('unserialize', array_unique(array_map('serialize', $inventory)));

    Cache::put('inventory', $uniqueInventory, Carbon::parse('next friday', 'America/Los_Angeles')->addHours(3)->addMinutes(59));

    return $uniqueInventory;
  }

  /**
   * @param  array $inventoryHash
   * @return array
   */
  private function getItemHashes($inventoryHash) {
    $itemHashes = [];
    $saleItemCategories = $inventoryHash['Response']['data']['saleItemCategories'];
    foreach($saleItemCategories as $itemCategory) {
      $saleItems = $itemCategory['saleItems'];
      foreach($saleItems as $item) {
        $hashId = (string)$item['item']['itemHash'];
        $itemHashes[] = $hashId;
      }
    }

    return $itemHashes;
  }

  /**
   * @param  array $hash
   * @return array
   */
  private function decodeItemHash($hashId) {
    $itemRes = $this->client->getJSON('Manifest/6/'.$hashId);

    return $itemRes['Response']['data']['inventoryItem'];
  }

}
