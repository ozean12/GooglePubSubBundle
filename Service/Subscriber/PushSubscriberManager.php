<?php

namespace Ozean12\GooglePubSubBundle\Service\Subscriber;

use Ozean12\GooglePubSubBundle\DTO\PushMessageRequestDTO;

/**
 * Class PushSubscriberManager
 */
class PushSubscriberManager
{
    /**
     * @var PushSubscriberInterface[]
     */
    private $subscribers;

    /**
     * @param PushMessageRequestDTO $messageRequest
     * @return bool
     */
    public function processMessage(PushMessageRequestDTO $messageRequest)
    {
        $subscription = $messageRequest->getSubscription();

        foreach ($this->subscribers as $subscriber) {
            if ($subscriber->getSubscription() === $subscription) {
                $subscriber->process($messageRequest->getMessage());

                return true;
            }
        }

        return false;
    }

    /**
     * @param PushSubscriberInterface $subscriber
     */
    public function addSubscriber(PushSubscriberInterface $subscriber)
    {
        $this->subscribers[] = $subscriber;
    }
}
