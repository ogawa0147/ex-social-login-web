<?php

namespace App\Helpers\Socials;

class Yahoojapan
{
    /*
     * Yahoo
     *
     * https://developer.yahoo.co.jp/yconnect/v2/
     */

    public static $name = 'yahoojapan';

    protected static $apis = [
        'authorization' => 'https://auth.login.yahoo.co.jp/yconnect/v2/authorization',
        'token'         => 'https://auth.login.yahoo.co.jp/yconnect/v2/token',
        'attribute'     => 'https://userinfo.yahooapis.jp/yconnect/v2/attribute',
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
        return 'openid profile email address';
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
            'bail'          => '1',
            'state'         => bin2hex(openssl_random_pseudo_bytes(32)),
            'nonce'         => bin2hex(openssl_random_pseudo_bytes(32)),
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
        $expires_in = $response['expires_in'];
        $id_token = $response['id_token'];
        $refresh_token = $response['refresh_token'];

        $response = static::requestAttribute($access_token);

        $results = [
            'loginProvider' => static::$name,
            'id'            => '',
            'birthday'      => '',
            'firstName'     => '',
            'lastName'      => '',
            'email'         => $response['email'],
            'gender'        => $response['gender'],
            'avatar'        => $response['picture'],
            'age'           => '',
            'birthDay'      => '',
            'birthMonth'    => '',
            'birthYear'     => $response['birthdate'],
            'nickname'      => $response['nickname'],
            'language'      => '',
            'name'          => $response['nickname'],
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
    private static function requestAttribute($access_token)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => static::$apis['attribute'],
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
