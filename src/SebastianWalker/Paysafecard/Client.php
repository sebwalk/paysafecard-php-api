<?php
namespace SebastianWalker\Paysafecard;

use SebastianWalker\Paysafecard\Exceptions\ApiError;
use SebastianWalker\Paysafecard\Exceptions\AuthenticationError;
use SebastianWalker\Paysafecard\Exceptions\NotFoundError;
use SebastianWalker\Paysafecard\Exceptions\PaymentError;
use Unirest\Request;

class Client
{
    /**
     * The testing base URL
     *
     * @var string
     */
    private static $BASEURL_TESTING = 'https://apitest.paysafecard.com/v1';

    /**
     * The production base URL
     *
     * @var string
     */
    private static $BASEURL_PRODUCTION = 'https://api.paysafecard.com/v1';

    /**
     * Your Paysafe API key
     *
     * @var string
     */
    private $apiKey = '';

    /**
     * The redirect and notification URLs for this client
     *
     * @var Urls
     */
    private $urls;

    /**
     * Whether the client accesses the testing or production system
     *
     * @var bool
     */
    private $testing = false;

    /**
     * Client constructor
     *
     * @param string $apiKey
     * @param Urls|null $urls
     * @param bool $testing
     */
    public function __construct($apiKey = '', $urls = null, $testing = false)
    {
        $this->setApiKey($apiKey);
        $this->setTestingMode($testing);
        if($urls!==null) $this->setUrls($urls);
    }

    /**
     * Send a request to the Paysafe servers
     *
     * @param string $method
     * @param string $resource
     * @param $data
     * @return \stdClass
     */
    public function sendRequest($method, $resource, $data = [])
    {
        $body = Request\Body::json($data);
        $response = Request::$method(
            $this->getResourceUrl($resource),
            $this->getHeaders(),
            $body
        );
        if($response->code > 200){
            $this->handleError($response);
        }
        return $response->body;
    }

    /**
     * Get the appropriate API base URL (testing or production)
     *
     * @return string
     */
    public function getApiUrl()
    {
        if($this->isTestingMode()){
            return static::$BASEURL_TESTING;
        }

        return static::$BASEURL_PRODUCTION;
    }

    /**
     * Get the API request URL for a specific resource (e.g. payments)
     *
     * @param string $resource
     * @return string
     */
    public function getResourceUrl($resource)
    {
        return $this->getApiUrl() . "/" . $resource;
    }

    /**
     * Get the API request headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return [
            'Content-Type'=>'application/json',
            'Authorization'=>"Basic ".base64_encode($this->getApiKey())
        ];
    }

    /**
     * Handle API errors
     *
     * @param $response
     * @throws ApiError
     * @throws AuthenticationError
     * @throws NotFoundError
     * @throws PaymentError
     */
    public function handleError($response)
    {
        $body = $response->body;
        switch($response->code){
            case 500:
                throw new ApiError("Technical error on Paysafecard's end");
            case 401:
                throw new AuthenticationError("Authentication failed due to missing or invalid API key (10008)");
            case 400:
                $number = $body->number;
                switch($number){
                    case 10028:
                        throw new ApiError("Invalid request parameter: ".$body->param." ".$body->message." (10028)");
                    case 2001:
                        throw new PaymentError("Transaction already exists (2001)");
                    case 2017:
                        throw new PaymentError("This payment is not capturable at the moment (2017)");
                    case 3001:
                        throw new PaymentError("Merchant is not active (3001)");
                    case 3007:
                        throw new PaymentError("Debit attempt after expiry of dispo time window (3007)");
                    default:
                        throw new ApiError("Unknown error (".$number.")");
                }
            case 404:
                throw new NotFoundError("Resource not found");
        }
    }

    /**
     * @param string $apiKey
     * @return $this
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * @return string
     */
    private function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param Urls $urls
     * @return $this
     */
    public function setUrls(Urls $urls)
    {
        $this->urls = $urls;
        return $this;
    }

    /**
     * @return Urls
     */
    public function getUrls()
    {
        return $this->urls;
    }

    /**
     * @param bool $testing
     * @return $this
     */
    public function setTestingMode($testing)
    {
        $this->testing = $testing;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTestingMode()
    {
        return $this->testing;
    }
}