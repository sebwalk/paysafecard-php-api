<?php
namespace SebastianWalker\Paysafecard;


class Urls
{
    /**
     * @var string
     */
    private $success_url = '';

    /**
     * @var string
     */
    private $failure_url = '';

    /**
     * @var string
     */
    private $notification_url = '';

    /**
     * Urls constructor
     *
     * @param array ...$urls
     */
    public function __construct(...$urls)
    {
        switch(sizeof($urls)){
            case 1:
                // success+failure+notification
                $this->setSuccessUrl($urls[0])->setFailureUrl($urls[0])->setNotificationUrl($urls[0]);
                break;
            case 2:
                // success+failure; notification
                $this->setSuccessUrl($urls[0])->setFailureUrl($urls[0])->setNotificationUrl($urls[1]);
                break;
            case 3:
                // success; failure; notification
                $this->setSuccessUrl($urls[0])->setFailureUrl($urls[1])->setNotificationUrl($urls[2]);
                break;
        }
    }

    /**
     * @return string
     */
    public function getSuccessUrl()
    {
        return $this->success_url;
    }

    /**
     * @param string $success_url
     * @return $this
     */
    public function setSuccessUrl($success_url)
    {
        $this->success_url = $success_url;
        return $this;
    }

    /**
     * @return string
     */
    public function getFailureUrl()
    {
        return $this->failure_url;
    }

    /**
     * @param string $failure_url
     * @return $this
     */
    public function setFailureUrl($failure_url)
    {
        $this->failure_url = $failure_url;
        return $this;
    }

    /**
     * @return string
     */
    public function getNotificationUrl()
    {
        return $this->notification_url;
    }

    /**
     * @param string $notification_url
     * @return $this
     */
    public function setNotificationUrl($notification_url)
    {
        $this->notification_url = $notification_url;
        return $this;
    }
}