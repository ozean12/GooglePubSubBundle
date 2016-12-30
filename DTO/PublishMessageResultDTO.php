<?php

namespace Ozean12\GooglePubSubBundle\DTO;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class PublishMessageResultDTO
 */
class PublishMessageResultDTO
{
    /**
     * @var string[]
     * @Serializer\Type("array<string>")
     * @Serializer\SerializedName("messageIds")
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
     */
    public function setMessageIds(array $messageIds)
    {
        $this->messageIds = $messageIds;
    }
}
