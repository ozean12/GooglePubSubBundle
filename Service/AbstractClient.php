<?php

namespace Ozean12\GooglePubSubBundle\Service;

use Google\Cloud\PubSub\PubSubClient;
use JMS\Serializer\Serializer;

/**
 * Class AbstractClient
 */
abstract class AbstractClient
{
    use LoggerTrait;

    /**
     * @var PubSubClient
     */
    protected $client;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * AbstractClient constructor.
     *
     * @param PubSubClient $client
     * @param Serializer   $serializer
     */
    public function __construct(PubSubClient $client, Serializer $serializer)
    {
        $this->client = $client;
        $this->serializer = $serializer;
    }
}
