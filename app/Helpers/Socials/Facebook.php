<?php

namespace App\Helpers\Socials;

class Facebook
{
    /*
     * Facebook
     *
     * https://github.com/facebook/php-graph-sdk
     * https://developers.facebook.com/docs
     */

    public static $name = 'facebook';

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
        return ['email'];
    }

    /**
     * SDKインスタンス
     *
     * @param
     * @return Facebook
     */
    private static function getClient()
    {
        session_start();
        $fb = new \Facebook\Facebook([
            'app_id'                => static::getClientId(),
            'app_secret'            => static::getClientSecret(),
            'default_graph_version' => 'v2.2',
        ]);
        return $fb;
    }

    /**
     * リダイレクトURL
     *
     * @param
     * @return String
     */
    private static function oauthLogin()
    {
        $fb = static::getClient();
        $helper = $fb->getRedirectLoginHelper();
        return $helper->getLoginUrl(static::getRedirectRri(), static::getScopes());
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

        $fb = static::getClient();
        $helper = $fb->getRedirectLoginHelper();
        $token = $helper->getAccessToken();
        $client = $fb->getOAuth2Client();
        $access_token = $client->getLongLivedAccessToken($token);

        $response = $fb->get('/me?fields=id,address,birthday,email,first_name,gender,last_name,locale,name,picture', $access_token);
        $user = $response->getGraphUser();

        $results = [
            'loginProvider' => static::$name,
            'id'            => $user->getId(),
            'birthday'      => $user->getBirthday(),
            'firstName'     => $user->getFirstName(),
            'lastName'      => $user->getLastName(),
            'email'         => $user->getEmail(),
            'gender'        => $user->getGender(),
            'avatar'        => $user->getPicture()->getUrl(),
            'age'           => '',
            'birthDay'      => '',
            'birthMonth'    => '',
            'birthYear'     => '',
            'nickname'      => '',
            'language'      => '',
            'name'          => $user->getName(),
            'displayName'   => '',
        ];

        return $results;
    }
}
