<?php
/**
 * Licensed under the MIT License
 * 
 *  Copyright (c) 2012 Sebastian Müller <info@sebastian-mueller.net>
 * 
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is
 *  furnished to do so, subject to the following conditions:
 * 
 *  The above copyright notice and this permission notice shall be included in
 *  all copies or substantial portions of the Software.
 * 
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */
 
if (!function_exists('json_decode'))
  throw Exception('Please install the json extension to use this class.');
  
if(!function_exists('curl_init'))
  throw Exception('Please install the php5-curl extension to use this class.');

/**
 * Goo.gl API wrapper class
 * 
 *
 * @author     Sebastian Müller <info@sebastian-mueller.net>
 * @link       http://www.sebastian-mueller.net
 */
class GooglShortener extends Exception
{
  /**
   * The version of GoogShortener
   *
   * @var string The version number
   **/
  const VERSION = '0.2.0';
  
  /**
   * Goo.gl API key
   * Request your API key here: <https://code.google.com/apis/console/>
   *
   * @var string
   **/
  private $api_key;
  
  /**
   * Goo.gl API URL
   *
   * @var string
   **/
  public static $API_URL = 'https://www.googleapis.com/urlshortener/v1/url?key=';
  
  /**
   * Init object with API key
   * 
   * @var $api_key string
   */
  public function __construct($api_key) {
    $this->api_key = $api_key;
  }
  
  /**
   * Curl opts to call the API
   *
   * @var array
   **/
  public static $CURL_OPTS = array(
      CURLOPT_USERAGENT      => 'GooglShortener',
      CURLOPT_CONNECTTIMEOUT => 5,
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_TIMEOUT        => 30
    );
  
  /**
   * Shorten a long URL
   *
   * @return mixed
   **/
  public function shorten($urls) {
    $returndata = null;
    
    if(is_array($urls)) {
      // shorten an array of long urls
      foreach($urls as $u) {
        $returndata[] = $this->callApi(array('longUrl' => $u), null, 'post');
      }
    } else {
      // shorten a single link
      $returndata = $this->callApi(array('longUrl' => $urls), null, 'post');
    }
    
    return $returndata;
  }
  
  /**
   * Expand one or more goo.gl Short URLs
   *
   * @return mixed
   * @author Sebastian Müller
   **/
  public function expand($urls)
  {
    $returndata = null;
    
    if(is_array($urls)) {
      // expand an array of long urls
      foreach($urls as $u) {
        $returndata[] = $this->callApi(null, array('shortUrl' => $u, 'projection' => 'FULL'), 'get');
      }
    } else {
      // shorten a single link
      $returndata = $this->callApi(null, array('shortUrl' => $urls, 'projection' => 'FULL'), 'get');
    }
    
    return $returndata;
  }
  
  /**
   * Converts an array to an object
   *
   * @return object
   * @author Sebastian Müller
   **/
  private function convertArrayToObject($arraydata) {
    $object = new stdClass();
    foreach ($arraydata as $a => $v) {
        $object->{$a} = $v;
    }
    return $object;
  }
  
  /**
   * Calls the Goo.gl API with curl
   *
   * @return string A JSON string
   **/
  private function callApi($post_data, $url_parameter, $method = 'post') {
    // the url to call the api
    $call_url = self::$API_URL.$this->api_key;
    $url_parameter_append = '';
    $post_json = '{';
    
    if(!empty($post_data) && !is_array($post_data)) {
      throw Exception('Please provide an array for the data');
    }
    
    // convert $post_data array to object and then to json
    if(!empty($post_data)) {
      $post_data = $this->convertArrayToObject($post_data);
      $post_json = json_encode($post_data);
    }
    
    // format url parameter
    if(!empty($url_parameter) && is_array($url_parameter)) {
      foreach($url_parameter as $key => $val) {
        $url_parameter_append .= '&'.$key.'='.$val;
      }
    }
    
    // init curl
    $curl = curl_init();
    
    curl_setopt($curl, CURLOPT_URL, $call_url.$url_parameter_append);
    // for some reason CURLOPT_HTTPHEADER don't work in curl_setopt_array...
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt_array($curl, self::$CURL_OPTS);
    
    if($method == 'post') {
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $post_json);
    } elseif($method == 'get') {
      curl_setopt($curl, CURLOPT_POST, false);
    } else {
      throw Exception('API call method must be post or get');
    }
    
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
    curl_setopt($curl, CURLOPT_HTTPHEADERS,array('Content-Type:application/json; Accept: application/json; charset=utf8'));
    
    // call API
    $result = curl_exec($curl);

    if ($result === false) {
      $exc = new Exception('Curl Error: '.curl_errno($curl).': '.curl_error($curl));
      curl_close($curl);
      throw $exc;
    }
    
    return json_decode($result);
  } 
}
