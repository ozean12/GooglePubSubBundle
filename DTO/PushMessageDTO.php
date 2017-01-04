<?php

namespace Ozean12\GooglePubSubBundle\DTO;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class PushMessageDTO
 */
class PushMessageDTO
{
    /**
     * @Serializer\Type("array<string>")
     *
     * @var string[]
     */
    private $attributes;

    /**
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $data;

    /**
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $messageId;

    /**
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $publishTime;

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * @return mixed
     */
    public function getPublishTime()
    {
        return $this->publishTime;
    }
}
