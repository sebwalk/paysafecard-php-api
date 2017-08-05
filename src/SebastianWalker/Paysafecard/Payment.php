<?php
namespace SebastianWalker\Paysafecard;


class Payment
{
    /**
     * @var string
     */
    public static $STATUS_INITIATED = 'INITIATED';

    /**
     * @var string
     */
    public static $STATUS_REDIRECTED = 'REDIRECTED';

    /**
     * @var string
     */
    public static $STATUS_AUTHORIZED = 'AUTHORIZED';

    /**
     * @var string
     */
    public static $STATUS_SUCCESS = 'SUCCESS';

    /**
     * @var string
     */
    public static $STATUS_CANCELED_MERCHANT = 'CANCELED_MERCHANT';

    /**
     * @var string
     */
    public static $STATUS_CANCELED_CUSTOMER = 'CANCELED_CUSTOMER';

    /**
     * @var string
     */
    public static $STATUS_EXPIRED = 'EXPIRED';

    /**
     * The Paysafe payment ID
     *
     * @var string
     */
    private $id;

    /**
     * The amount of the payment
     *
     * @var Amount
     */
    private $amount;

    /**
     * The Paysafe payment status
     * https://www.paysafecard.com/fileadmin/api/de.html#/reference/payment-information
     *
     * @var string
     */
    private $status;

    /**
     * An ID from your own system that uniquely identifies this customer
     *
     * @var string
     */
    private $customer_id;

    /**
     * The Paysafe payment page URL. Redirect the user to this URL to authorize.
     *
     * @var string
     */
    private $auth_url = '';

    /**
     * Payment constructor
     *
     * @param Amount|null $amount
     * @param string|null $customer_id
     */
    public function __construct($amount = null, $customer_id = null)
    {
        if($amount===null) $amount = new Amount;
        if($customer_id===null) $customer_id = '';

        $this->setAmount($amount);
        $this->setCustomerId($customer_id);
    }

    /**
     * Initiate the payment using the provided client
     *
     * @param Client $client
     * @return $this
     */
    public function create(Client $client){
        $result = $client->sendRequest("post","payments", [
            'type'=>'PAYSAFECARD',
            'amount'=>$this->getAmount()->getAmount(),
            'currency'=>$this->getAmount()->getCurrency(),
            'redirect'=>[
                'success_url'=>$client->getUrls()->getSuccessUrl(),
                'failure_url'=>$client->getUrls()->getFailureUrl()
            ],
            'notification_url'=>$client->getUrls()->getNotificationUrl(),
            'customer'=>[
                'id'=>$this->getCustomerId()
            ]
        ]);
        $this->fill($result);
        return $this;
    }

    /**
     * Capture the payment using the provided client
     *
     * @param Client $client
     * @return $this
     */
    public function capture(Client $client){
        if($this->isAuthorized()){
            $result = $client->sendRequest("post","payments/".$this->getId()."/capture");
            $this->fill($result);
        }
        return $this;
    }

    /**
     * Find an existing payment
     *
     * @param string $id
     * @param Client $client
     * @return $this
     */
    public static function find($id, Client $client){
        $result = $client->sendRequest("get","payments/".$id);
        $payment = new Payment();
        $payment->fill($result);
        return $payment;
    }

    /**
     * Fill the object with data fetched from the API
     *
     * @param \stdClass $result
     */
    public function fill($result)
    {
        $this->setId($result->id);
        $this->setAmount(new Amount($result->amount, $result->currency));
        $this->setStatus($result->status);
        $this->setAuthUrl(isset($result->redirect->auth_url)?$result->redirect->auth_url:'');
        $this->setCustomerId($result->customer->id);
    }

    /**
     * @return bool
     */
    public function isInitiated()
    {
        return $this->getStatus() === static::$STATUS_INITIATED;
    }

    /**
     * @return bool
     */
    public function isRedirected()
    {
        return $this->getStatus() === static::$STATUS_REDIRECTED;
    }

    /**
     * @return bool
     */
    public function isCancelled()
    {
        return $this->getStatus() === static::$STATUS_CANCELED_CUSTOMER || $this->getStatus() === static::$STATUS_CANCELED_MERCHANT;
    }

    /**
     * @return bool
     */
    public function isExpired()
    {
        return $this->getStatus() === static::$STATUS_EXPIRED;
    }

    /**
     * @return bool
     */
    public function isAuthorized()
    {
        return $this->getStatus() === static::$STATUS_AUTHORIZED;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->getStatus() === static::$STATUS_SUCCESS;
    }

    /**
     * Shorthand for all statuses that indicate a failed payment (cancelled + expired)
     *
     * @return bool
     */
    public function isFailed()
    {
        return $this->isCancelled() || $this->isExpired();
    }

    /**
     * Shorthand for all statuses that indicate a payment waiting to be authorized
     *
     * @return bool
     */
    public function isWaiting()
    {
        return $this->isInitiated() || $this->isRedirected();
    }

    /**
     * @param string $id
     * @return $this
     */
    private function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Amount $amount
     * @return $this
     */
    public function setAmount(Amount $amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return Amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param string $status
     * @return $this
     */
    private function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $customer_id
     * @return $this
     */
    public function setCustomerId($customer_id)
    {
        $this->customer_id = $customer_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerId()
    {
        return $this->customer_id;
    }

    /**
     * @param string $auth_url
     * @return $this
     */
    public function setAuthUrl($auth_url)
    {
        $this->auth_url = $auth_url;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthUrl()
    {
        return $this->auth_url;
    }
}