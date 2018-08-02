<?php

namespace App\Helpers\Socials;

use Google_Client;

class Google
{
    /*
     * Google
     *
     * https://github.com/google/google-api-php-client
     * https://developers.google.com/api-client-library/php/
     */

    public static $name = 'google';

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
        return [\Google_Service_Plus::USERINFO_EMAIL, \Google_Service_Plus::USERINFO_PROFILE];
    }

    /**
     * SDKインスタンス
     *
     * @param
     * @return Facebook
     */
    private static function getClient()
    {
        $client = new Google_Client();
        $client->setClientId(static::getClientId());
        $client->setClientSecret(static::getClientSecret());
        $client->setRedirectUri(static::getRedirectRri());
        $client->setAccessType('offline');
        $client->setState(bin2hex(openssl_random_pseudo_bytes(32)));
        $client->setScopes(static::getScopes());
        return $client;
    }

    /**
     * リダイレクトURL
     *
     * @param
     * @return String
     */
    private static function oauthLogin()
    {
        $client = static::getClient();
        return $client->createAuthUrl();
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

        $client = static::getClient();
        $client->authenticate($_GET['code']);
        $access_token = $client->getAccessToken();

        $client->setAccessToken($access_token);
        $service = new \Google_Service_Plus($client);
        $person = $service->people->get('me');

        $results = [
            'loginProvider' => static::$name,
            'birthday'      => $person->getBirthday(),
            'firstName'     => $person->getName()['familyName'],
            'lastName'      => $person->getName()['givenName'],
            'email'         => $person->getEmails()[0]['value'],
            'gender'        => $person->getGender(),
            'avatar'        => $person->getImage()['url'],
            'age'           => $person->getAgeRange(),
            'birthDay'      => '',
            'birthMonth'    => '',
            'birthYear'     => '',
            'nickname'      => $person->getNickname(),
            'language'      => $person->getLanguage(),
            'name'          => $person->getDisplayName(),
            'displayName'   => $person->getDisplayName(),
        ];

        return $results;
    }
}
