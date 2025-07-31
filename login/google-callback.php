<?php
session_start();

require_once 'vendor/autoload.php';

$client = new $_Google_Client();
$client->setClientId("YOUR_CLIENT_ID");
$client->setClientSecret("YOUR_CLIENT_SECRET");
$client->setRedirectUri("http://localhost/google-login/google-callback.php");

$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);

    $google_oauth = new $_Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();

    $_SESSION['email'] = $google_account_info->email;
    $_SESSION['name'] = $google_account_info->name;

    echo "Welcome, " . $_SESSION['name'] . " (" . $_SESSION['email'] . ")";
} else {
    echo "Login failed!";
}
?>
