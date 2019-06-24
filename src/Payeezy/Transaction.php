<?php

class Payeezy_Transaction
{
  private $url;

  private $baseURL;

  private $apiKey;

  private $apiSecret;

  private $merchantToken;

  public function __construct($client)
  {
    $this->baseURL = $client->getUrl();
    $this->apiKey = $client->getApiKey();
    $this->apiSecret = $client->getApiSecret();
    $this->merchantToken = $client->getMerchantToken();
  }

  /**
   * Payeezy
   *
   * HMAC Authentication
   */

  public function hmacAuthorizationToken($payload)
  {

    $nonce = strval(hexdec(bin2hex(openssl_random_pseudo_bytes(4, $cstrong))));

    $timestamp = strval(time()*1000); //time stamp in milli seconds

    $data = $this->apiKey . $nonce . $timestamp . $this->merchantToken . $payload;

    $hashAlgorithm = "sha256";

    $hmac = hash_hmac($hashAlgorithm, $data, $this->apiSecret, false);    // HMAC Hash in hex

    $authorization = base64_encode($hmac);

    return array(
      'authorization' => $authorization,
      'nonce' => $nonce,
      'timestamp' => $timestamp,
      'apikey' => $this->apiKey,
      'token' => $this->merchantToken,
    );
  }

  /**
   * jsonpp - Pretty print JSON data
   *
   * In versions of PHP < 5.4.x, the json_encode() function does not yet provide a
   * pretty-print option. In lieu of forgoing the feature, an additional call can
   * be made to this function, passing in JSON text, and (optionally) a string to
   * be used for indentation.
   *
   * @param string $json  The JSON data, pre-encoded
   * @param string $istr  The indentation string
   *
   * @return string
   */
  public function jsonpp($json, $istr = '  ')
  {
      $result = '';
    for ($p=$q=$i=0; isset($json[$p]); $p++) {
      $json[$p] == '"' && ($p>0?$json[$p-1]:'') != '\\' && $q=!$q;
      if (strchr('}]', $json[$p]) && !$q && $i--) {
        strchr('{[', $json[$p-1]) || $result .= "\n".str_repeat($istr, $i);
      }
      $result .= $json[$p];
      if (strchr(',{[', $json[$p]) && !$q) {
        $i += strchr('{[', $json[$p])===false?0:1;
        strchr('}]', $json[$p+1]) || $result .= "\n".str_repeat($istr, $i);
      }
    }
      return $result;
  }

  /**
   * Payeezy
   *
   * Post Transaction
   */

  public function postTransaction($payload, $headers)
  {

    $request = curl_init();
    curl_setopt($request, CURLOPT_URL, $this->url);
    curl_setopt($request, CURLOPT_POST, true);
    curl_setopt($request, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($request, CURLOPT_HEADER, false);
    //curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt(
        $request,
        CURLOPT_HTTPHEADER,
        array(
        'Content-Type: application/json',
        'apikey:'.strval($this->apiKey),
        'token:'.strval($this->merchantToken),
        'Authorization:'.$headers['authorization'],
        'nonce:'.$headers['nonce'],
        'timestamp:'.$headers['timestamp'],
        )
    );

    $response = curl_exec($request);

    if (false === $response) {
        echo curl_error($request);
    }

    //$httpcode = curl_getinfo($request, CURLINFO_HTTP_CODE);
    curl_close($request);

    return $response;
  }
  
  /**
   * Use this for primary transactions like Authorize, Purchase
   * @param transactionRequest
   * @return
   * @throws Exception
   */
  
  public function doPrimaryTransaction($args = array())
  {
    $this->url = $this->baseURL;
    $payload = json_encode($args);
    $headerArray = $this->hmacAuthorizationToken($payload);
  
    $response_in_JSON = $this->postTransaction($payload, $headerArray);
    $response = json_decode($response_in_JSON);
    if (isset($response->Error)) {
      throw new Payeezy_Error($response->correlation_id, $response->Error);
    }
    return $response;
  }

  /**
   * Use this for Secondary transactions like void, refund, capture etc
   */

  public function doSecondaryTransaction($transaction_id, $args = array())
  {
    $this->url = $this->baseURL . '/' . $transaction_id;
    $payload = json_encode($args, JSON_FORCE_OBJECT);
    $headerArray = $this->hmacAuthorizationToken($payload);
    $response_in_JSON = $this->postTransaction($payload, $headerArray);
    $response = json_decode($response_in_JSON);
    if (isset($response->Error)) {
      throw new Payeezy_Error($response->correlation_id, $response->Error);
    }
    return $response;
  }
}//end of class
