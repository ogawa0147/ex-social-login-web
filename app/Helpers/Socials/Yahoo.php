<?php

namespace App\Helpers\Socials;

class Yahoo
{
    /*
     * Yahoo
     *
     * https://developer.yahoo.com/oauth/
     */

    public static $name = 'yahoo';

    protected static $apis = [
        'authorization' => 'https://api.login.yahoo.com/oauth2/request_auth',
        'token'         => 'https://api.login.yahoo.com/oauth2/get_token',
        'attribute'     => 'https://social.yahooapis.com/v1',
    ];

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
        return 'http://example.com/auth2/callback/' . static::$name;
    }

    /**
     * パーミッションスコープ
     *
     * @param
     * @return Array
     */
    private static function getScopes()
    {
        return 'openid';
    }

    /**
     * リダイレクトURL
     *
     * @param
     * @return String
     */
    private static function oauthLogin()
    {
        $params = array(
            'client_id'     => static::getClientId(),
            'redirect_uri'  => static::getRedirectRri(),
            'scope'         => static::getScopes(),
            'response_type' => 'code',
            'nonce'         => bin2hex(openssl_random_pseudo_bytes(10)),
            'language'      => 'ja',
            'prompt'        => 'login',
        );
        return static::$apis['authorization'] . '?' . http_build_query($params);
    }

    /**
     * 属性
     *
     * @param
     * @return Array
     */
    private static function oauthAccess()
    {
        if (!isset($_GET['code']))
        {
            return ['errer' => 'error'];
        }

        $response = static::requestToken($_GET['code']);

        $access_token = $response['access_token'];
        $refresh_token = $response['refresh_token'];
        $expires_in = $response['expires_in'];
        $xoauth_yahoo_guid = $response['xoauth_yahoo_guid'];
        $id_token = $response['id_token'];

        $response = static::requestAttribute($xoauth_yahoo_guid, $access_token);

        $results = [
            'loginProvider' => static::$name,
            'id'            => $response['profile']['guid'],
            'birthday'      => '',
            'firstName'     => $response['profile']['familyName'],
            'lastName'      => $response['profile']['givenName'],
            'email'         => $response['profile']['emails'][0]['handle'],
            'gender'        => $response['profile']['gender'],
            'avatar'        => $response['profile']['image']['imageUrl'],
            'age'           => '',
            'birthDay'      => '',
            'birthMonth'    => '',
            'birthYear'     => '',
            'nickname'      => $response['profile']['nickname'],
            'language'      => $response['profile']['lang'],
            'name'          => $response['profile']['familyName'] . $response['profile']['givenName'],
            'displayName'   => '',
        ];

        return $results;
    }

    /**
     * AccessToken新規API
     *
     * @param
     * @return Array
     */
    private static function requestToken($code)
    {
        $params = array(
            'grant_type'   => 'authorization_code',
            'redirect_uri' => static::getRedirectRri(),
            'code'         => $code,
        );

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => static::$apis['token'],
            CURLOPT_HEADER         => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => http_build_query($params),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded', 'Authorization: Basic ' . base64_encode(static::getClientId() . ':' . static::getClientSecret())],
            CURLOPT_TIMEOUT        => 5,
        ]);
        $exec = curl_exec($curl);
        $getinfo = curl_getinfo($curl);
        curl_close($curl);

        $body = substr($exec, $getinfo['header_size']);

        return json_decode($body, true);
    }

    /**
     * AccessToken更新API
     *
     * @param
     * @return Array
     */
    private static function requestRefreshToken($refresh_token)
    {
        $params = array(
            'grant_type'    => 'refresh_token',
            'redirect_uri'  => static::getRedirectRri(),
            'refresh_token' => $refresh_token,
        );

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => static::$apis['token'],
            CURLOPT_HEADER         => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => http_build_query($params),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded', 'Authorization: Basic ' . base64_encode(static::getClientId() . ':' . static::getClientSecret())],
            CURLOPT_TIMEOUT        => 5,
        ]);
        $exec = curl_exec($curl);
        $getinfo = curl_getinfo($curl);
        curl_close($curl);

        $body = substr($exec, $getinfo['header_size']);

        return json_decode($body, true);
    }

    /**
     * 属性API
     *
     * @param
     * @return Array
     */
    private static function requestAttribute($guid, $access_token)
    {
        $params = array(
            'format' => 'json'
        );

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => sprintf(static::$apis['attribute'] . '/user/%s/profile?format=json', $guid),
            CURLOPT_HEADER         => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded', 'Authorization: Bearer ' . $access_token],
            CURLOPT_TIMEOUT        => 5,
        ]);
        $exec = curl_exec($curl);
        $getinfo = curl_getinfo($curl);
        curl_close($curl);

        $body = substr($exec, $getinfo['header_size']);

        return json_decode($body, true);
    }

    /**
     * 期限切れチェック
     *
     * @param
     * @return Bool
     */
    private static function isExpiredToken($expires_in=null)
    {
        if (is_null($expires_in))
        {
            return false;
        }
        $date = new \DateTime();
        $expire = new \DateTime("+{$expires_in} second");
        return $date < $expire;
    }
}
