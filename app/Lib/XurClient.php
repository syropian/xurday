<?php

namespace XurDay\Lib;

use XurDay\Exceptions\XurNotPresentException;
use XurDay\Lib\HTTPClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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
    Cache::forget('inventory');

    $inventory = [];
    $inventoryHash = $this->client->getJSON('Advisors/Xur/');

    if ($inventoryHash['ErrorStatus'] == 'DestinyVendorNotFound') {
      throw new XurNotPresentException();
    }

    $itemHashes = $this->getItemHashes($inventoryHash);
    foreach($itemHashes as $category => $hashes) {
      foreach($hashes as $itemHash){
        $inventory[$category][] = [
          'item' => $this->decodeItemHash($itemHash['itemHash']),
          'itemCosts' => $itemHash['itemCosts'],
          'stackSize' => $itemHash['stackSize']
        ];
      }
    }
    Cache::forever('inventory', $inventory);

    return $inventory;
  }

  /**
   * @param  array $inventoryHash
   * @return array
   */
  private function getItemHashes($inventoryHash) {
    $items = [];
    $saleItemCategories = $inventoryHash['Response']['data']['saleItemCategories'];

    // Iterate over each category of item (Curios, Material Exchange, Exotic Gear)
    foreach($saleItemCategories as $itemCategory) {
      $categoryName = $itemCategory['categoryTitle'];
      $saleItems = $itemCategory['saleItems'];
      $items[$categoryName] = [];
      // Grab the item hash and its cost data
      foreach($saleItems as $item) {
        $costs = [];
        $hashId = (string)$item['item']['itemHash'];
        foreach($item['costs'] as $cost) {
          $currency = $this->decodeItemHash($cost['itemHash']);
          $costs[] = [
            'value' => $cost['value'],
            'currency' => $currency['itemName'],
          ];
        }
        // Tack on the stack size as well
        $items[$categoryName][] = [
          'itemHash' => $hashId,
          'itemCosts' => $costs,
          'stackSize' => $item['item']['stackSize']
        ];
      }
    }

    return $items;
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
