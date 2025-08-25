<?php
session_start();

$clientID = "YOUR_CLIENT_ID";
$clientSecret = "YOUR_CLIENT_SECRET";
$redirectUri = "http://localhost/google-login/google-callback.php";

require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

$authUrl = $client->createAuthUrl();
header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
?>
