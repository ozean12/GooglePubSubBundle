<?php

namespace Ozean12\GooglePubSubBundle\Service\Publisher;

use Google\Cloud\Exception\ConflictException;
use Google\Cloud\PubSub\PubSubClient;
use Google\Cloud\PubSub\Topic;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;
use Ozean12\GooglePubSubBundle\DTO\MessageDataDTOInterface;
use Ozean12\GooglePubSubBundle\DTO\PublishMessageResultDTO;
use Ozean12\GooglePubSubBundle\Service\AbstractClient;

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
     * @param string       $topic
     * @param PubSubClient $client
     * @param Serializer   $serializer
     * @param string       $suffix
     */
    public function __construct($topic, PubSubClient $client, Serializer $serializer, string $suffix = '')
    {
        parent::__construct($client, $serializer);

        $this->topicName = $topic.$suffix;
    }

    /**
     * @param MessageDataDTOInterface $data
     * @param array                   $attributes
     * @param array                   $options
     * @param array                   $publisherOptions
     * @return PublishMessageResultDTO
     */
    public function publish(
        MessageDataDTOInterface $data,
        array $attributes = [],
        $options = [],
        $publisherOptions = []
    ) {
        $this->setupTopic();

        $context = (new SerializationContext())->setSerializeNull(true);
        if (!empty($publisherOptions['serializationGroups'])) {
            $context->setGroups($publisherOptions['serializationGroups']);
        }

        $message = [
            'data' => $this->serializer->serialize($data, 'json', $context),
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
