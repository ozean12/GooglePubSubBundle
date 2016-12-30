<?php

namespace Ozean12\GooglePubSubBundle\Service;

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
     * @param string     $subscription
     * @param array      $config
     * @param Serializer $serializer
     */
    public function __construct($subscription, array $config, Serializer $serializer)
    {
        parent::__construct($serializer, $config);

        $this->subscription = $subscription;
    }
}
