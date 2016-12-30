<?php

namespace Ozean12\GooglePubSubBundle\Tests\DTO;

/**
 * Class PublishMessageResultDTO
 */
class TestPublishMessageResultDTO
{
    /**
     * @var string[]
     */
    private $messageIds;

    /**
     * @return string[]
     */
    public function getMessageIds(): array
    {
        return $this->messageIds;
    }

    /**
     * @param string[] $messageIds
     * @return TestPublishMessageResultDTO
     */
    public function setMessageIds(array $messageIds)
    {
        $this->messageIds = $messageIds;

        return $this;
    }
}
