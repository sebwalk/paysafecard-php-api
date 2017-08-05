<?php
namespace SebastianWalker\Paysafecard;


class Amount
{
    /**
     * The amount as a number with max. two decimal places
     *
     * @var string|null
     */
    private $amount;

    /**
     * The currency as a 3-character ISO 4217 currency code
     *
     * @var string|null
     */
    private $currency;

    /**
     * Amount constructor
     *
     * @param double|int|null $amount
     * @param string|null $currency
     */
    public function __construct($amount = 0, $currency = 'EUR')
    {
        $this->setAmount($amount);
        $this->setCurrency($currency);
    }

    /**
     * @param double $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return double
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }
}