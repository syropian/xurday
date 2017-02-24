<?php

namespace XurDay\Lib;
use GuzzleHttp\Client;

class HTTPClient {

  /**
   * @var Client
   */
  protected $client;

  /**
   * @param string $base
   * @param array $headers
   */
  public function __construct($base = '', $headers = []) {
    $this->client = new Client([
      'base_uri' => $base,
      'headers' => $headers
    ]);
  }

  /**
   * @param  string $path
   * @return array
   */
  public function getJSON($path) {
    return json_decode($this->client->request('GET', $path)->getBody()->getContents(), true);
  }
}
