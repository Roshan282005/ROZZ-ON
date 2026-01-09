<?php
require_once 'vendor/autoload.php';

use Google\Client as Google_Client;
use Google\Service\Oauth2 as Google_Service_Oauth2;

session_start();

$client = new Google_Client();
$client->setClientId("YOUR_CLIENT_ID");
$client->setClientSecret("YOUR_CLIENT_SECRET");
$client->setRedirectUri("http://localhost/google-login/google-callback.php");

$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (isset($token['error'])) {
        echo "Error fetching access token: " . htmlspecialchars($token['error']);
        exit();
    }
    $client->setAccessToken($token['access_token']);

    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();

    $_SESSION['email'] = $google_account_info->email;
    $_SESSION['name'] = $google_account_info->name;

    echo "Welcome, " . $_SESSION['name'] . " (" . $_SESSION['email'] . ")";
} else {
    echo "Login failed!";
}
?>
