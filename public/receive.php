<?php

// ここら辺の設定値はバレないように隠しておいてください
$client_id = '';
$client_secret = '';
$access_token = '';
$access_token_secret = '';
$account_verify_credentials_uri = 'https://api.twitter.com/1.1/account/verify_credentials.json';

$oauth_token = $_GET['oauth_token'];
$oauth_verifier = $_GET['oauth_verifier'];

$method = 'GET';
$params = array(
    'oauth_nonce'            => md5(uniqid(rand(), true)),
    'oauth_signature_method' => 'HMAC-SHA1',
    'oauth_timestamp'        => time(),
    'oauth_consumer_key'     => $client_id,
    'oauth_version'          => '1.0',
    'oauth_token'            => $access_token,
);
$query = array(
    'include_email' => 'true',
    'include_entities'=> 'false',
);
$params = array_merge($params, $query);
uksort($params, 'strnatcmp');

$base = implode('&', array_map('rawurlencode', [$method, $account_verify_credentials_uri, str_replace(array('+', '%7E'), array('%20', '~'), http_build_query($params, '', '&'))]));
$key = implode('&', array_map('rawurlencode', [$client_secret, $access_token_secret]));
$params['oauth_signature'] = base64_encode(hash_hmac('sha1', $base, $key, true));

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL            => $account_verify_credentials_uri . '?' . http_build_query($query),
    CURLOPT_HEADER         => true,
    CURLOPT_CUSTOMREQUEST  => $method,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => ['Authorization: OAuth ' . http_build_query($params, '', ',')],
    CURLOPT_TIMEOUT        => 5,
]);
$exec = curl_exec($curl);
$getinfo = curl_getinfo($curl);
curl_close($curl);

$header = substr($exec, 0, $getinfo['header_size']);
$body = substr($exec, $getinfo['header_size']);
$response = json_decode($body, true);

echo "<pre>";
var_dump($response);
exit();

// $redirect_uri = 'http://local.social/oauth/complete/twitter';
// $query = array(
//     'oauth_token' => $_GET['oauth_token'],
//     'oauth_verifier'=> $_GET['oauth_verifier'],
// );
// header('location: ' . $redirect_uri . '?' . http_build_query($query));
// exit();
