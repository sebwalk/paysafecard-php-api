# paysafecard-php-api
A PHP wrapper for the [Paysafecard](https://www.paysafecard.com) Payments REST API

## Installation
Install the library using composer:
```
composer require sebastianwalker/paysafecard-php-api
```

## Usage
### Initiating a payment
```php
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
```

### Capturing a payment
```php
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
```

## API

### Setup Client
```php
// Create new client object
$client = new Client([string $apiKey], [Urls $urls], [bool $testingMode]);

// Getters and setters
$client->setApiKey(string $apiKey);
$client->setUrls(Urls $urls);
$client->setTestingMode(bool $testingMode);
```

### Setup Payment
```php
// Create new payment object
$payment = new Payment(Amount $amount, string $customerId);

// Find existing payment
$payment = Payment::find(string $id, Client $client);

// Initiate payment
$payment->create(Client $client);

// Capture payment
$payment->capture(Client $client);

// Getter and setters
$payment->setAmount(Amount $amount);
$payment->getAmount();
$payment->setCustomerId(string $customerId);
$payment->getCustomerId();
$payment->getAuthUrl(bool $testingMode);
$payment->getStatus();

// Checking for standard payment statuses
$payment->isInitiated();
$payment->isRedirected();
$payment->isCancelled();
$payment->isExpired();
$payment->isAuthorized();
$payment->isSuccessful();

// Shorthands
$payment->isFailed(); // cancelled or expired
$payment->isWaiting(); // initiated or redirected
```

### Setup Amount
```php
// Create new amount object
$amount = new Amount(double $amount, string $currency); // e.g. (10.00, "EUR")

// Getters and setters
$amount->setAmount(double $amount);
$amount->getAmount();
$amount->setCurrency(string $currency);
$amount->getCurrency();
```

### Setup URLs
```php
// Create new url object
$urls = new Urls(string $url); // use given URL for success + failure + notification
$urls = new Urls(string $url, string $url2); // use first URL for success + failure, second for notification
$urls = new Urls(string $url, string $url2, string $url3); // use first URL for success, second for failure, third for notification

// Getters and setters
$urls->setSuccessUrl(string $url);
$urls->getSuccessUrl();
$urls->setFailureUrl(string $url);
$urls->getFailure();
$urls->setNotificationUrl(string $url);
$urls->getNotificationUrl();
```
