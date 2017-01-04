<?php

namespace Ozean12\GooglePubSubBundle\Service\Subscriber;

use Ozean12\GooglePubSubBundle\DTO\PushMessageDTO;

/**
 * Interface PushSubscriberInterface
 */
interface PushSubscriberInterface
{
    /**
     * @param PushMessageDTO $message
     * @return mixed
     */
    public function process(PushMessageDTO $message);

    /**
     * @return string
     */
    public function getSubscription();
}
