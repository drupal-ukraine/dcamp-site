<?php

namespace Drupal\commerce_liqpay\Controller;

use Drupal\Component\Serialization\Json;

/**
 * Payment method liqpay process.
 */
class LiqPayStatusRequestSender {

  const CURRENCY_EUR = 'EUR';

  const CURRENCY_USD = 'USD';

  const CURRENCY_UAH = 'UAH';

  const CURRENCY_RUB = 'RUB';

  const CURRENCY_RUR = 'RUR';

  /**
   * API URL.
   *
   * @var array|mixed|null
   */
  private $apiUrl;

  /**
   * Checkout url.
   *
   * @var array|mixed|null
   */
  private $checkoutUrl;

  /**
   * API version.
   *
   * @var array|mixed|null
   */
  private $version;

  /**
   * An store public key.
   *
   * @var mixed
   */
  private $publicKey;

  /**
   * An store private key.
   *
   * @var mixed
   */
  private $privateKey;

  /**
   * Server response code.
   *
   * @var null
   */
  private $serverResponseCode = NULL;

  /**
   * Supported currencies.
   *
   * @var array
   */
  protected $supportedCurrencies = [
    self::CURRENCY_EUR,
    self::CURRENCY_USD,
    self::CURRENCY_UAH,
    self::CURRENCY_RUB,
    self::CURRENCY_RUR,
  ];

  /**
   * LiqPayStatusRequestSender constructor.
   *
   * @param array $store_data
   *   Keys:
   *    - public_key;
   *    - private_key;
   *    - api_url;
   *    - checkout_url;
   *    - version.
   *
   * @throws InvalidArgumentException
   */
  public function __construct(array $store_data) {
    if (empty($store_data['public_key'])) {
      throw new InvalidArgumentException('public_key is empty');
    }
    if (empty($store_data['private_key'])) {
      throw new InvalidArgumentException('private_key is empty');
    }

    $this->publicKey = $store_data['public_key'];
    $this->privateKey = $store_data['private_key'];

    $config            = \Drupal::config('commerce_liqpay.settings');
    $this->apiUrl      = isset($store_data['api_url']) ? $store_data['api_url'] : $config->get('commerce_liqpay.api_url');
    $this->checkoutUrl = isset($store_data['checkout_url']) ? $store_data['checkout_url'] : $config->get('commerce_liqpay.checkout_url');
    $this->version     = isset($store_data['version']) ? $store_data['version'] : $config->get('commerce_liqpay.version');
  }

  /**
   * Call API.
   *
   * @param string $path
   *   The API path.
   * @param array $params
   *   The request params.
   * @param int $timeout
   *   Connection timeout.
   *
   * @return array|bool
   *   The array response's parameters.
   */
  public function api($path, array $params = [], $timeout = 5) {
    if (!isset($params['version'])) {
      $params['version'] = $this->version;
    }
    $url         = $this->apiUrl . $path;
    $public_key  = $this->publicKey;
    $private_key = $this->privateKey;
    $data        = $this->encodeParams(array_merge(compact('public_key'), $params));
    $signature   = $this->strToSign($private_key . $data . $private_key);
    $postfields  = http_build_query([
      'data'      => $data,
      'signature' => $signature,
    ]);

    $client = \Drupal::httpClient();
    $request = $client->request('POST', $url, [
      'timeout'         => $timeout,
      'connect_timeout' => $timeout,
      'body'            => $postfields,
      'verify'          => TRUE,

    ]);
    $server_output            = $request->getBody();
    $this->serverResponseCode = $request->getStatusCode();

    return $this->decodeParams($server_output);
  }

  /**
   * Return last api response http code.
   *
   * @return string|null
   *   http code.
   */
  public function getResponseCode() {
    return $this->serverResponseCode;
  }

  /**
   * Check currency.
   *
   * @param string $currency
   *   The currency code.
   *
   * @return bool
   *   FALSE on error validation TRUE otherwise.
   */
  public function isSupportedCurrency($currency) {
    if (in_array($currency, $this->supportedCurrencies)) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Encoding params.
   *
   * @param array $params
   *   Decoded data.
   *
   * @return string
   *   Encoded data.
   */
  private function encodeParams(array $params) {
    return base64_encode(Json::encode($params));
  }

  /**
   * Decoding json data from LiqPay service.
   *
   * @param string $data_encoded
   *   Encoded data.
   *
   * @return bool|mixed
   *   Array on successful validation FALSE otherwise.
   */
  public function decodeParams($data_encoded) {
    if (!empty($data_encoded)) {
      if ($data = Json::decode($data_encoded)) {
        if (!empty($data)) {
          return $data;
        }
      }
    }
    return FALSE;
  }

  /**
   * Encoding string.
   *
   * @param string $str
   *   Encoded string.
   *
   * @return string
   *   Encoded string for API.
   */
  public function strToSign($str) {
    $signature = base64_encode(sha1($str, 1));
    return $signature;
  }

}
