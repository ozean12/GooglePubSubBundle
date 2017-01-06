<?php

namespace Ozean12\GooglePubSubBundle\Tests\Service;

use Ozean12\GooglePubSubBundle\DTO\PushMessageDTO;
use Ozean12\GooglePubSubBundle\DTO\PushMessageRequestDTO;
use Ozean12\GooglePubSubBundle\Service\Subscriber\PushSubscriberInterface;
use Ozean12\GooglePubSubBundle\Service\Subscriber\PushSubscriberManager;

/**
 * Class PushSubscriberManagerTest
 */
class PushSubscriberManagerTest extends \PHPUnit_Framework_TestCase
{
    const GOOD_SUBSCRIPTION = 'test_sub';
    const BAD_SUBSCRIPTION = 'some_other_sub';
    const PROJECT_ID = 'amazing-project';

    /**
     * @dataProvider processMessageDataProvider
     *
     * @param PushSubscriberInterface[] $subscribers
     * @param bool                      $expectedResult
     * @param int                       $expectedLoggerCalls
     * @param PushMessageRequestDTO     $messageRequest
     * @param string                    $testCase
     */
    public function testProcessMessage(
        array $subscribers,
        $expectedResult,
        $expectedLoggerCalls,
        $messageRequest,
        $testCase
    ) {
        $manager = new PushSubscriberManager(self::PROJECT_ID);

        foreach ($subscribers as $subscriptionName => $subscriber) {
            $manager->addSubscriber($subscriptionName, $subscriber);
        }

        if (!empty($expectedLoggerCalls)) {
            $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')
                ->disableOriginalConstructor()
                ->getMock()
            ;

            $logger
                ->expects($this->exactly($expectedLoggerCalls))
                ->method('info')
            ;

            $manager->setLogger($logger);
        }

        $this->assertEquals($expectedResult, $manager->processMessage($messageRequest), $testCase);
    }

    /**
     * @return array
     */
    public function processMessageDataProvider()
    {
        $message = new PushMessageDTO();
        $message->setData('test');

        $messageRequest = new PushMessageRequestDTO();
        $messageRequest
            ->setMessage($message)
            ->setSubscription(sprintf('projects/%s/subscriptions/%s', self::PROJECT_ID, self::GOOD_SUBSCRIPTION))
        ;

        $badProcessor = $this
            ->getMockBuilder('Ozean12\GooglePubSubBundle\Service\Subscriber\PushSubscriberInterface')
            ->getMock()
        ;

        $badProcessor->expects($this->never())->method('process');

        $goodProcessor = $this
            ->getMockBuilder('Ozean12\GooglePubSubBundle\Service\Subscriber\PushSubscriberInterface')
            ->getMock()
        ;

        $goodProcessor->expects($this->once())->method('process')->with($message);

        $goodProcessorWithLogger = $this
            ->getMockBuilder('Ozean12\GooglePubSubBundle\Service\Subscriber\PushSubscriberInterface')
            ->getMock()
        ;

        $goodProcessorWithLogger->expects($this->once())->method('process')->with($message);

        return [
            [[
                self::BAD_SUBSCRIPTION => $badProcessor
            ], false, 0, $messageRequest, 'No subscriber found'],

            [[
                self::BAD_SUBSCRIPTION => $badProcessor,
                self::GOOD_SUBSCRIPTION => $goodProcessor,
            ], true, 0, $messageRequest, 'Subscriber found'],

            [[
                self::BAD_SUBSCRIPTION => $badProcessor,
                self::GOOD_SUBSCRIPTION => $goodProcessorWithLogger
            ], true, 1, $messageRequest, 'Subscriber found with logger'],
        ];
    }
}
