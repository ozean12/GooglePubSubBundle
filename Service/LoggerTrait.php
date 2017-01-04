<?php

namespace Ozean12\GooglePubSubBundle\Service;

use Psr\Log\LoggerInterface;

/**
 * Class LoggerTrait
 */
trait LoggerTrait
{
    /**
     * @var LoggerInterface
     */
    private $logger;

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
