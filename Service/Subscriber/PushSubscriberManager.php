<?php

namespace Ozean12\GooglePubSubBundle\Service\Subscriber;

use Ozean12\GooglePubSubBundle\DTO\PushMessageRequestDTO;
use Ozean12\GooglePubSubBundle\Service\LoggerTrait;
use Psr\Log\LoggerInterface;

/**
 * Class PushSubscriberManager
 */
class PushSubscriberManager
{
    use LoggerTrait;

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
        $message = $messageRequest->getMessage();

        if (isset($this->subscribers[$subscription])) {
            $subscriber = $this->subscribers[$subscription];
            $subscriber->process($message);

            $this->logInfo('Received message : {subscription}[{message}]; Processed with {subscriberClass} subscriber', [
                'message' => $message->getMessageId(),
                'subscription' => $subscription,
                'subscriberClass' => get_class($subscriber),
            ]);

            return true;
        }

        $this->logInfo('Received message : {subscription}[{message}]; Subscriber not found', [
            $message->getMessageId(),
            'subscription' => $subscription,
        ]);

        return false;
    }

    /**
     * @param string                  $subscriptionName
     * @param PushSubscriberInterface $subscriber
     */
    public function addSubscriber($subscriptionName, PushSubscriberInterface $subscriber)
    {
        $this->subscribers[$subscriptionName] = $subscriber;
    }
}
