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
}
