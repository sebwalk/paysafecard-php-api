<?php

use SebastianWalker\Paysafecard\Client;
use SebastianWalker\Paysafecard\Amount;
use SebastianWalker\Paysafecard\Urls;
use SebastianWalker\Paysafecard\Payment;

include("../vendor/autoload.php");

// Set up the API Client
$client = new Client("psc_apikey_goes_here");
$client->setUrls(new Urls("http://localhost/examples/CapturePayment.php?payment_id={payment_id}"));
$client->setTestingMode(true);

// Initiate the payment
$amount = new Amount(20.00, "EUR");
$payment = new Payment($amount, "customer123");
$payment->create($client);

// Redirect to Paysafecard payment page
header("Location: ".$payment->getAuthUrl());