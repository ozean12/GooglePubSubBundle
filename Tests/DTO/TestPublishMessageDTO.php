<?php

namespace Ozean12\GooglePubSubBundle\Tests\DTO;

use Ozean12\GooglePubSubBundle\DTO\MessageDataDTOInterface;

/**
 * Class TestPublishMessageDTO
 */
class TestPublishMessageDTO implements MessageDataDTOInterface
{
    /**
     * @var string
     */
    private $testMessage;

    /**
     * TestPublishMessageDTO constructor.
     *
     * @param string $testMessage
     */
    public function __construct($testMessage = null)
    {
        $this->testMessage = $testMessage;
    }

    /**
     * @return string
     */
    public function getTestMessage()
    {
        return $this->testMessage;
    }

    /**
     * @param string $testMessage
     * @return TestPublishMessageDTO
     */
    public function setTestMessage(string $testMessage)
    {
        $this->testMessage = $testMessage;

        return $this;
    }
}
