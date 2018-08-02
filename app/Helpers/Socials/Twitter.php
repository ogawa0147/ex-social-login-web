<?php

namespace App\Helpers\Socials;

use Abraham\TwitterOAuth\TwitterOAuth;

class Twitter
{
    /*
     * Twitter
     *
     * https://github.com/abraham/twitteroauth
     * https://twitteroauth.com/
     */

    public static $name = 'twitter';

    /**
     * ログインURLを取得
     *
     * @param
     * @return String
     */
    public static function login()
    {
        return static::oauthLogin();
    }

    /**
     * 属性取得
     *
     * @param
     * @return Array
     */
    public static function access()
    {
        return static::oauthAccess();
    }

    /**
     * クライアントID
     *
     * @param
     * @return String
     */
    private static function getClientId()
    {
        return \Config::get('services.' . static::$name . '.client_id');
    }

    /**
     * クライアントシークレット
     *
     * @param
     * @return String
     */
    private static function getClientSecret()
    {
        return \Config::get('services.' . static::$name . '.client_secret');
    }

    /**
     * コールバックURL
     *
     * @param
     * @return String
     */
    private static function getRedirectRri()
    {
        return 'http://example.com/login/social/callback/' . static::$name;
    }

    /**
     * パーミッションスコープ
     *
     * @param
     * @return Array
     */
    private static function getScopes()
    {
        return ['include_email' => 'true', 'include_entities'=> 'false'];
    }

    /**
     * リダイレクトURL
     *
     * @param
     * @return String
     */
    private static function oauthLogin()
    {
        $connection = new TwitterOAuth(static::getClientId(), static::getClientSecret());
        $request_token = $connection->oauth('oauth/request_token', ['oauth_callback' => static::getRedirectRri()]);
        \Session::put('oauth_token', $request_token['oauth_token']);
        \Session::put('oauth_token_secret', $request_token['oauth_token_secret']);
        return $connection->url('oauth/authenticate', ['oauth_token' => $request_token['oauth_token']]);
    }

    /**
     * 属性
     *
     * @param
     * @return Array
     */
    private static function oauthAccess()
    {
        $request_token = [];
        $request_token['oauth_token'] = \Session::get('oauth_token');
        $request_token['oauth_token_secret'] = \Session::get('oauth_token_secret');
        if (isset($_REQUEST['oauth_token']) && $request_token['oauth_token'] !== $_REQUEST['oauth_token'])
        {
            return ['errer' => 'error'];
        }

        $connection = new TwitterOAuth(static::getClientId(), static::getClientSecret(), $request_token['oauth_token'], $request_token['oauth_token_secret']);
        $access_token = $connection->oauth('oauth/access_token', ['oauth_verifier' => $_REQUEST['oauth_verifier']]);

        \Session::put('oauth_token', $access_token['oauth_token']);
        \Session::put('oauth_token_secret', $access_token['oauth_token_secret']);

        $connection = new TwitterOAuth(static::getClientId(), static::getClientSecret(), $access_token['oauth_token'], $access_token['oauth_token_secret']);
        $user = $connection->get('account/verify_credentials', static::getScopes());

        $results = [
            'loginProvider' => static::$name,
            'id'            => $user->id,
            'birthday'      => '',
            'firstName'     => '',
            'lastName'      => '',
            'email'         => $user->email,
            'gender'        => '',
            'avatar'        => $user->profile_image_url,
            'age'           => '',
            'birthDay'      => '',
            'birthMonth'    => '',
            'birthYear'     => '',
            'nickname'      => '',
            'language'      => $user->lang,
            'name'          => $user->name,
            'displayName'   => $user->screen_name,
        ];

        return $results;
    }

    /*
    * 以下自前
    * echo "<pre>";print_r(\Auth::user());exit();
    */
    // private static function _oauth_request_token()
    // {
    //     $method = 'POST';
    //     $params = array(
    //         'oauth_nonce'            => md5(uniqid(rand(), true)),
    //         'oauth_callback'         => static::$redirect_uri,
    //         'oauth_signature_method' => 'HMAC-SHA1',
    //         'oauth_timestamp'        => time(),
    //         'oauth_consumer_key'     => static::$client_id,
    //         'oauth_version'          => '1.0',
    //     );
    //     uksort($params, 'strnatcmp');
    //     $base = static::_signature_base($method, static::$request_token_uri, $params);
    //     $key = static::_signature_key(static::$client_secret, '');
    //     $params['oauth_signature'] = static::_signature_encode($base, $key);

    //     $curl = curl_init();
    //     curl_setopt_array($curl, [
    //         CURLOPT_URL            => static::$request_token_uri,
    //         CURLOPT_HEADER         => true,
    //         CURLOPT_CUSTOMREQUEST  => $method,
    //         CURLOPT_SSL_VERIFYPEER => false,
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_HTTPHEADER     => ['Authorization: OAuth ' . http_build_query($params, '', ',')],
    //         CURLOPT_TIMEOUT        => 5,
    //     ]);
    //     $exec = curl_exec($curl);
    //     $getinfo = curl_getinfo($curl);
    //     curl_close($curl);

    //     $header = substr($exec, 0, $getinfo['header_size']);
    //     $body = substr($exec, $getinfo['header_size']);

    //     $response = [];
    //     parse_str($body, $response);

    //     return $response;
    // }

    // private static function _oauth_authorize()
    // {
    //     session_start();

    //     $request_token = static::_oauth_request_token();

    //     $_SESSION = [];

    //     $_SESSION['_oauth_token'] = $request_token['oauth_token'];
    //     $_SESSION['_oauth_token_secret'] = $request_token['oauth_token_secret'];
    //     $_SESSION['_authorize'] = true;

    //     $query = array(
    //         'oauth_token' => $_SESSION['_oauth_token'],
    //     );

    //     return static::$oauth_url.'?'.http_build_query($query);
    // }

    // private static function _account_verify_credentials()
    // {
    //     session_start();

    //     $oauth_token = $_GET['oauth_token'];
    //     $oauth_verifier = $_GET['oauth_verifier'];

    //     $session['oauth_token'] = $_SESSION['_oauth_token'];
    //     $oauth_token_secret = $_SESSION['_oauth_token_secret'];

    //     $_SESSION = [];

    //     $method = 'GET';
    //     $params = array(
    //         'oauth_nonce'            => md5(uniqid(rand(), true)),
    //         'oauth_signature_method' => 'HMAC-SHA1',
    //         'oauth_timestamp'        => time(),
    //         'oauth_consumer_key'     => static::$client_id,
    //         'oauth_version'          => '1.0',
    //         'oauth_token'            => static::$access_token,
    //     );
    //     $query = array(
    //         'include_email' => 'true',
    //         'include_entities'=> 'false',
    //     );
    //     $params = array_merge($params, $query);
    //     uksort($params, 'strnatcmp');

    //     $base = static::_signature_base($method, static::$credentials_uri, $params);
    //     $key = static::_signature_key(static::$client_secret, static::$access_token_secret);
    //     $params['oauth_signature'] = static::_signature_encode($base, $key);

    //     $curl = curl_init();
    //     curl_setopt_array($curl, [
    //         CURLOPT_URL            => static::$credentials_uri . '?' . http_build_query($query),
    //         CURLOPT_HEADER         => true,
    //         CURLOPT_CUSTOMREQUEST  => $method,
    //         CURLOPT_SSL_VERIFYPEER => false,
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_HTTPHEADER     => ['Authorization: OAuth ' . http_build_query($params, '', ',')],
    //         CURLOPT_TIMEOUT        => 5,
    //     ]);
    //     $exec = curl_exec($curl);
    //     $getinfo = curl_getinfo($curl);
    //     curl_close($curl);

    //     $header = substr($exec, 0, $getinfo['header_size']);
    //     $body = substr($exec, $getinfo['header_size']);
    //     $response = json_decode($body, true);

    //     return $response;
    // }

    // private static function _signature_base($method, $url, $params)
    // {
    //     return implode('&', array_map('rawurlencode', [$method, $url, str_replace(array('+', '%7E'), array('%20', '~'), http_build_query($params, '', '&'))]));
    // }

    // private static function _signature_key($client_secret, $access_token_secret = '')
    // {
    //     return implode('&', array_map('rawurlencode', [$client_secret, $access_token_secret]));
    // }

    // private static function _signature_encode($base, $key)
    // {
    //     return base64_encode(hash_hmac('sha1', $base, $key, true));
    // }
}
