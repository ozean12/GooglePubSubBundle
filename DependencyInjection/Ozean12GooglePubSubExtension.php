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

        $baseDefinition = $container->getDefinition(self::CLIENT_SERVICE_DEFINITION);

        $definitions = [];
        $clientConfig = [
            'projectId' => $config['project_id'],
            'keyFilePath' => $config['key_file_path'],
        ];

        foreach ($config['topics'] as $topic) {
            $definitions[self::PUBLISHER_SERVICE_DEFINITION.$topic] = $this->createClientDefinition(
                $baseDefinition,
                $topic,
                $clientConfig,
                Publisher::class
            );
        }

        foreach ($config['subscriptions'] as $subscription) {
            $definitions[self::SUBSCRIBER_SERVICE_DEFINITION.$subscription] = $this->createClientDefinition(
                $baseDefinition,
                $subscription,
                $clientConfig,
                Subscriber::class
            );
        }

        $container->setDefinitions($definitions);
    }

    /**
     * @param Definition $baseDefinition
     * @param string     $topicOrSubscription
     * @param array      $clientConfig
     * @param string     $class
     * @return Definition
     */
    private function createClientDefinition(
        Definition $baseDefinition,
        $topicOrSubscription,
        array $clientConfig,
        $class
    ) {
        return (clone $baseDefinition)
            ->replaceArgument(0, $topicOrSubscription)
            ->replaceArgument(1, $clientConfig)
            ->setClass($class)
            ->setPublic(true)
            ->addTag(self::TAG_NAME)
        ;
    }
}
