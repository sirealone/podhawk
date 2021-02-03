<?php

/**
 * @author Sachin Khosla - @realin
 * @desc This is the wrapper class, contains all the high level functions
 * responsible of making all the calls to & fro to twitter
 * Class modified by Peter Carter to permit use with Yahoo/Delicious OAuth API
 * including refreshing Yahoo access tokens (which expire after 1 hour)
 */


require_once 'OAuth.php';
//require_once 'config.php';

class TwitterAPI
{

  private $requestTokenURL;
  private $accessTokenURL;
  private $loginURL;
  private $service, $consumer, $token, $result, $sha1_method;
  public $return_code;


  function __construct($service, $consumer_key, $consumer_secret, $oauth_token = NULL, $oauth_token_secret = NULL)
  {
	switch ($service)
	{
		case 'twitter':
		$this->requestTokenURL = 'http://twitter.com/oauth/request_token';
		$this->accessTokenURL = 'http://twitter.com/oauth/access_token';
		$this->loginURL = 'http://twitter.com/oauth/authorize';
		break;

		case 'delicious':
		$this->requestTokenURL = 'https://api.login.yahoo.com/oauth/v2/get_request_token';
		$this->accessTokenURL = 'https://api.login.yahoo.com/oauth/v2/get_token';
		break;
	}
	
	$this->service = $service;

    // define the supported SHA1 method
    $this->sha1_method = new OAuthSignatureMethod_HMAC_SHA1();

    $this->consumer = new OAuthConsumer($consumer_key, $consumer_secret);

    if (!empty($oauth_token) && !empty($oauth_token_secret))
      $this->token = new OAuthConsumer($oauth_token, $oauth_token_secret);
    else
      $this->token = NULL;
  }

  /**
   * @param <string> $verify is the OAUTH_VERIFIER passed in the callback url
   * or the 'session handle' needed to refresh a Yahoo access token
   * @return access token
   */

  function getAccessToken($verify = '', $refresh = false )
  {
    $data = array();

	if ($refresh)
	{
		$data['oauth_session_handle'] = $verify;
	}
    elseif ($verify != '')
    {
      $data['oauth_verifier'] = $verify;
    }
    $request = $this->makeRequest($this->accessTokenURL,false, $data);

    $token = OAuthUtil::parse_parameters($request);
    $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
    return $token;
  }

  /**
   *
   * @param <string> $url URL to send the get request
   * @param <array> $data anydata to be sent
   */

  function doGet($url, $data = array())
  {

    $response = $this->makeRequest($url, false, $data);
    return $response;
  }

  /**
   *
   * @param <string> $url URL to send the POST request
   * @param <array> $data anydata to be sent
   */

  function doPost($url, $data = array())
  {
    $response = $this->makeRequest($url, true, $data);
    return $response;
  }

  /**
   * gets the request token for the first time
   */
  function getRequestToken($oauth_callback = NULL)
  {
    $params = array();
    if (!empty($oauth_callback))
    {
      $params['oauth_callback'] = $oauth_callback;
    }
    $request = $this->makeRequest($this->requestTokenURL,false, $params);
    $token = OAuthUtil::parse_parameters($request);
    $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
    return $token;
  }

  /**
   * Creates the Authorization/login URL
   */
  function getLoginURL($token)
  {
    $token = $token['oauth_token'];
	return $this->loginURL . "?oauth_token={$token}";
  }

  /**
   * Prepares the request for the CURL
   */
  function makeRequest($url, $is_post = false, $data = array())
  {

    $method = ($is_post == true)?'POST':'GET';
    $request = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $method, $url, $data);
    $request->sign_request($this->sha1_method, $this->consumer, $this->token);

    if($is_post === true)
    {
      return $this->makeCurl($request->get_normalized_http_url(), $method, $request->to_postdata());
    }
    else
      return $this->makeCurl($request->to_url());
  }

  /**
   * Does all the CURL request, capable of sending both GET & POST requests
   */
  private function makeCurl($url,$is_post = false,$data=null)
  {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_USERAGENT, 'TwitterOAuth v0.2.0-beta2');
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

    if (!empty($data) && $is_post == true)
    {
      curl_setopt($curl, CURLOPT_POST, TRUE);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }

    curl_setopt($curl, CURLOPT_URL, $url);
    $this->result = curl_exec($curl);
    $this->return_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    return $this->result;
  }
}

?>
