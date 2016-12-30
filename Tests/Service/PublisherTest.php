<?php

namespace Ozean12\GooglePubSubBundle\Tests\Service;

use Google\Cloud\Exception\ConflictException;
use Google\Cloud\PubSub\PubSubClient;
use Google\Cloud\PubSub\Topic;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Ozean12\GooglePubSubBundle\Service\Publisher;
use Ozean12\GooglePubSubBundle\Tests\DTO\TestPublishMessageDTO;
use Ozean12\GooglePubSubBundle\Tests\DTO\TestPublishMessageResultDTO;

/**
 * Class PublisherTest
 */
class PublisherTest extends \PHPUnit_Framework_TestCase
{
    const TOPIC = 'test_topic';

    /**
     * @var Serializer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $serializer;

    /**
     * SetUp
     */
    public function setUp()
    {
        $this->serializer = $this->getMockBuilder('JMS\Serializer\Serializer')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        parent::setUp();
    }

    /**
     * @dataProvider getTestPublishData
     *
     * @param Topic|\PHPUnit_Framework_MockObject_MockObject        $topic
     * @param PubSubClient|\PHPUnit_Framework_MockObject_MockObject $client
     * @param bool                                                  $setLogger
     * @param int                                                   $logInfoCallsCount
     * @param string                                                $message
     */
    public function testPublish(Topic $topic, PubSubClient $client, $setLogger, $logInfoCallsCount, $message)
    {
        $publisher = new Publisher(self::TOPIC, $client, $this->serializer);

        $message = new TestPublishMessageDTO(uniqid('test_'));
        $result = (new TestPublishMessageResultDTO())
            ->setMessageIds([uniqid('test_')])
        ;

        $serializer = SerializerBuilder::create()->build();
        $serializedData = $serializer->serialize($message, 'json');
        $serializedResult = $serializer->toArray($result);

        $this->serializer
            ->expects($this->once())
            ->method('serialize')
            ->with($message, 'json')
            ->willReturn($serializedData)
        ;

        $this->serializer
            ->expects($this->once())
            ->method('fromArray')
            ->with($serializedResult)
            ->willReturn($result)
        ;

        $topic
            ->expects($this->once())
            ->method('publish')
            ->with(['data' => $serializedData], [])
            ->willReturn($serializedResult)
        ;

        if ($setLogger) {
            $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')
                ->disableOriginalConstructor()
                ->getMock()
            ;

            $logger
                ->expects($this->exactly($logInfoCallsCount))
                ->method('info')
            ;

            $publisher->setLogger($logger);
        }

        $actualResult = $publisher->publish($message);
        $this->assertEquals($result, $actualResult);
    }

    /**
     * @return array
     */
    public function getTestPublishData()
    {
        $newTopic = $this->getTopicMock();
        $newTopicWithLogger = $this->getTopicMock();
        $existingTopic = $this->getTopicMock();
        $existingTopicWithLogger = $this->getTopicMock();

        $newTopicClient = $this->getClientMock();
        $newTopicClientWithLogger = $this->getClientMock();
        $existingTopicClient = $this->getClientMock();
        $existingTopicClientWithLogger = $this->getClientMock();

        $newTopicClient->expects($this->once())->method('createTopic')->with(self::TOPIC)->willReturn($newTopic);
        $newTopicClientWithLogger->expects($this->once())->method('createTopic')->with(self::TOPIC)->willReturn($newTopicWithLogger);

        $existingTopicClient->expects($this->once())->method('createTopic')->willThrowException(new ConflictException('test'));
        $existingTopicClient->expects($this->once())->method('topic')->with(self::TOPIC)->willReturn($existingTopic);

        $existingTopicClientWithLogger->expects($this->once())->method('createTopic')->willThrowException(new ConflictException('test'));
        $existingTopicClientWithLogger->expects($this->once())->method('topic')->with(self::TOPIC)->willReturn($existingTopicWithLogger);

        return [
            [$newTopic, $newTopicClient, false, 0, 'New topic'],
            [$newTopicWithLogger, $newTopicClientWithLogger, true, 2, 'New topic with logger'],
            [$existingTopic, $existingTopicClient, false, 0, 'Existing topic'],
            [$existingTopicWithLogger, $existingTopicClientWithLogger, true, 1, 'Existing topic with logger'],
        ];
    }

    /**
     * @return Topic|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getTopicMock()
    {
        return $this->getMockBuilder('Google\Cloud\PubSub\Topic')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    /**
     * @return PubSubClient|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getClientMock()
    {
        return $this->getMockBuilder('Google\Cloud\PubSub\PubSubClient')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
}
