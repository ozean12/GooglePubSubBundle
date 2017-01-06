<?php

namespace Ozean12\GooglePubSubBundle\DTO;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class PushMessageRequestDTO
 */
class PushMessageRequestDTO
{
    /**
     * @Serializer\Type("Ozean12\GooglePubSubBundle\DTO\PushMessageDTO")
     *
     * @var PushMessageDTO
     */
    private $message;

    /**
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $subscription;

    /**
     * @return PushMessageDTO
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @param PushMessageDTO $message
     * @return PushMessageRequestDTO
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @param string $subscription
     * @return PushMessageRequestDTO
     */
    public function setSubscription($subscription)
    {
        $this->subscription = $subscription;

        return $this;
    }
}
