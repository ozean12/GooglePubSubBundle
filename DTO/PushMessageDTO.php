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

    /**
     * @param \string[] $attributes
     * @return PushMessageDTO
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param string $data
     * @return PushMessageDTO
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param string $messageId
     * @return PushMessageDTO
     */
    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;

        return $this;
    }

    /**
     * @param string $publishTime
     * @return PushMessageDTO
     */
    public function setPublishTime($publishTime)
    {
        $this->publishTime = $publishTime;

        return $this;
    }
}
