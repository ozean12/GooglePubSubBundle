<?php

namespace Ozean12\GooglePubSubBundle\Service;

use Google\Cloud\Exception\ConflictException;
use Google\Cloud\PubSub\Topic;
use JMS\Serializer\Serializer;
use Ozean12\GooglePubSubBundle\DTO\MessageDataDTOInterface;
use Ozean12\GooglePubSubBundle\DTO\PublishMessageResultDTO;

/**
 * Class Publisher
 */
class Publisher extends AbstractClient
{
    /**
     * @var Topic
     */
    private $topic;

    /**
     * @var string
     */
    private $topicName;

    /**
     * Publisher constructor.
     *
     * @param string     $topic
     * @param array      $config
     * @param Serializer $serializer
     */
    public function __construct($topic, array $config, Serializer $serializer)
    {
        parent::__construct($serializer, $config);

        $this->topicName = $topic;
    }

    /**
     * @param MessageDataDTOInterface $data
     * @param array                   $attributes
     * @param array                   $options
     * @return PublishMessageResultDTO
     */
    public function publish(MessageDataDTOInterface $data, array $attributes = [], $options = [])
    {
        $this->setupTopic();

        $message = [
            'data' => $this->serializer->serialize($data, 'json'),
        ];

        if (!empty($attributes)) {
            $message['attributes'] = $attributes;
        }

        $result = $this->topic->publish($message, $options);
        /** @var PublishMessageResultDTO $resultDTO */
        $resultDTO = $this->serializer->fromArray($result, PublishMessageResultDTO::class);

        $this->logInfo('Message(s) {messages} submitted to topic {topic}', [
            'messages' => join(', ', $resultDTO->getMessageIds()),
            'topic' => $this->topicName,
        ]);

        return $resultDTO;
    }

    /**
     * Create or fetch topic
     */
    private function setupTopic()
    {
        if ($this->topic instanceof Topic) {
            return;
        }

        try {
            $this->topic = $this->client->createTopic($this->topicName);
            $this->logInfo('New topic {topic} created', ['topic' => $this->topicName]);
        } catch (ConflictException $exception) { // topic already exists
            $this->topic = $this->client->topic($this->topicName);
        }
    }
}
