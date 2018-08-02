<?php

namespace App\Http\Controllers\Social\OAuth;

class OAuth_Line
{
	protected static $client_id       = '＜クライアントID＞';
	protected static $client_secret   = '＜シークレットキー＞';
	protected static $oauth_url       = 'https://access.line.me/dialog/oauth/weblogin';
	protected static $redirect_uri    = 'http://example.com/result.php';
	protected static $response_type   = 'code';

	function __construct() {}
	function __destruct() {}

	public static function oauth_login()
	{
		return static::_make_oauth_url();
	}

	private static function _make_oauth_url()
	{
		$querys = array(
			'client_id'     => static::$client_id,
			'redirect_uri'  => static::$redirect_uri,
			'response_type' => static::$response_type,
			'state'         => bin2hex(openssl_random_pseudo_bytes(32)),
		);
		return static::$oauth_url.'?'.http_build_query($querys);
	}
}
