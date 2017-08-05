<?php

use SebastianWalker\Paysafecard\Client;
use SebastianWalker\Paysafecard\Payment;

include("../vendor/autoload.php");

// Set up the API Client
$client = new Client("psc_apikey_goes_here");
$client->setTestingMode(true);

// Find the payment the user was redirected from
$payment = Payment::find($_GET["payment_id"], $client);

// Check if the payment was authorized
if($payment->isAuthorized()){
    // ... and capture it
    $payment->capture($client);

    if($payment->isSuccessful()){
        echo "Capture Successful!";
    }else{
        echo "Payment Failed (".$payment->getStatus().")";
    }

} else if($payment->isFailed()){
    echo "Payment Failed (".$payment->getStatus().")";

} else{
    echo "Other Status (".$payment->getStatus().")";

}