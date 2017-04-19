<?php

namespace Ozean12\GooglePubSubBundle\Service\Subscriber;

use Ozean12\GooglePubSubBundle\DTO\PushMessageRequestDTO;
use Ozean12\GooglePubSubBundle\Service\LoggerTrait;

/**
 * Class PushSubscriberManager
 */
class PushSubscriberManager
{
    use LoggerTrait;

    /**
     * @var string
     */
    private $projectId;

    /**
     * @var PushSubscriberInterface[]
     */
    private $subscribers;

    /**
     * @var string
     */
    private $suffix;

    /**
     * PushSubscriberManager constructor.
     *
     * @param string $projectId
     * @param string $suffix
     */
    public function __construct($projectId, string $suffix = '')
    {
        $this->projectId = $projectId;
        $this->suffix = $suffix;
    }

    /**
     * @param PushMessageRequestDTO $messageRequest
     * @return bool
     */
    public function processMessage(PushMessageRequestDTO $messageRequest)
    {
        $subscription = str_replace(
            sprintf('projects/%s/subscriptions/', $this->projectId),
            '',
            $messageRequest->getSubscription()
        );
        // Remove suffix from subscription name
        $subscription = preg_replace('/^(.*)'.preg_quote($this->suffix, '/').'$/', "\\1", $subscription);

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
            'message' => $message->getMessageId(),
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
