<?php

namespace XurDay\Lib;

use XurDay\Exceptions\XurNotPresentException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class XurClient {
  protected $client;

  public function __construct($key) {
    $this->client = new Client([
      'base_uri' => 'https://www.bungie.net/Platform/Destiny/',
      'headers' => ['X-API-Key' => $key],
    ]);
  }

  public function getInventory() {
    $inventoryRes = $this->client->request('GET', 'Advisors/Xur/');
    $inventoryHash = json_decode($inventoryRes->getBody()->getContents(), true);
    // dd($inventoryHash);
    if ($inventoryHash['ErrorStatus'] == 'DestinyVendorNotFound') {
      throw new XurNotPresentException();
    }
    $itemHashes = $this->getItemHashes($inventoryHash);
    $decodedItemHashes = $this->decodeItemHashes($itemHashes);
    $uniqueItemHashes = array_map('unserialize', array_unique(array_map('serialize', $decodedItemHashes)));
    Cache::put('inventory', $uniqueItemHashes, Carbon::parse('next friday', 'America/Los_Angeles')->addHours(3)->addMinutes(59));
    return $decodedItemHashes;
  }

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

  private function decodeItemHashes($itemHashes) {
    $items = [];
    foreach($itemHashes as $hashId) {
      $hashRes = $this->client->request('GET', 'Manifest/6/'.$hashId);
      $hashBody = json_decode($hashRes->getBody()->getContents(), true);
      $itemName = $hashBody['Response']['data']['inventoryItem']['itemName'];
      $itemType = $hashBody['Response']['data']['inventoryItem']['itemTypeName'];
      $itemTier = $hashBody['Response']['data']['inventoryItem']['tierTypeName'];
      $itemIcon = 'https://www.bungie.net'.$hashBody['Response']['data']['inventoryItem']['icon'];
      $items[] = [
        'name' => $itemName,
        'type' => $itemType,
        'tier' => $itemTier,
        'icon' => $itemIcon
      ];
    }

    return $items;
  }
}
