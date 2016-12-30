<?php

namespace Ozean12\GooglePubSubBundle\DependencyInjection;

use Ozean12\GooglePubSubBundle\Service\Publisher;
use Ozean12\GooglePubSubBundle\Service\Subscriber;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * {@inheritdoc}
 */
class Ozean12GooglePubSubExtension extends Extension
{
    const PUBSUB_CLIENT_SERVICE_DEFINITION = 'ozean12_google_pubsub.pubsub_client.service';
    const CLIENT_SERVICE_DEFINITION = 'ozean12_google_pubsub.client.service';
    const PUBLISHER_SERVICE_DEFINITION = 'ozean12_google_pubsub.publisher.';
    const SUBSCRIBER_SERVICE_DEFINITION = 'ozean12_google_pubsub.subscriber.';
    const TAG_NAME = 'ozean12_pub_sub_client';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $definitions = [];
        $clientConfig = [
            'projectId' => $config['project_id'],
            'keyFilePath' => $config['key_file_path'],
        ];

        $pubSubDefinition = $container
            ->getDefinition(self::PUBSUB_CLIENT_SERVICE_DEFINITION)
            ->replaceArgument(0, $clientConfig)
        ;

        $baseDefinition = $container->getDefinition(self::CLIENT_SERVICE_DEFINITION);


        foreach ($config['topics'] as $topic) {
            $definitions[self::PUBLISHER_SERVICE_DEFINITION.$topic] = $this->createClientDefinition(
                $baseDefinition,
                $pubSubDefinition,
                $topic,
                Publisher::class
            );
        }

        foreach ($config['subscriptions'] as $subscription) {
            $definitions[self::SUBSCRIBER_SERVICE_DEFINITION.$subscription] = $this->createClientDefinition(
                $baseDefinition,
                $pubSubDefinition,
                $subscription,
                Subscriber::class
            );
        }

        $container->setDefinitions($definitions);
    }

    /**
     * @param Definition $baseDefinition
     * @param Definition $pubSubClientDefinition
     * @param string     $topicOrSubscription
     * @param string     $class
     * @return Definition
     */
    private function createClientDefinition(
        Definition $baseDefinition,
        Definition $pubSubClientDefinition,
        $topicOrSubscription,
        $class
    ) {
        return (clone $baseDefinition)
            ->replaceArgument(0, $topicOrSubscription)
            ->replaceArgument(1, $pubSubClientDefinition)
            ->setClass($class)
            ->setPublic(true)
            ->addTag(self::TAG_NAME)
        ;
    }
}
