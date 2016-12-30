<?php

namespace Ozean12\GooglePubSubBundle\Service;

use Google\Cloud\PubSub\PubSubClient;
use Google\Cloud\PubSub\Subscription;
use JMS\Serializer\Serializer;

/**
 * Class Subscriber
 *
 * ! WIP !
 */
class Subscriber extends AbstractClient
{
    /**
     * @var Subscription
     */
    private $subscription;

    /**
     * Subscriber constructor.
     *
     * @param string       $subscription
     * @param PubSubClient $client
     * @param Serializer   $serializer
     */
    public function __construct($subscription, PubSubClient $client, Serializer $serializer)
    {
        parent::__construct($client, $serializer);

        $this->subscription = $subscription;
    }
}
