<?php

namespace Ozean12\GooglePubSubBundle\Service;

use Google\Cloud\PubSub\PubSubClient;
use JMS\Serializer\Serializer;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractClient
 */
abstract class AbstractClient
{
    /**
     * @var PubSubClient
     */
    protected $client;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var LoggerInterface
     */
    private $logger;

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

    /**
     * @param LoggerInterface $logger
     * @return AbstractClient
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * A proxy to logger->info() method
     * Ensures that logging is enabled
     *
     * @param string $message
     * @param array  $context
     */
    protected function logInfo($message, $context)
    {
        if (is_null($this->logger)) {
            return;
        }

        $this->logger->info($message, $context);
    }
}
