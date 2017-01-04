<?php

namespace Ozean12\GooglePubSubBundle\DependencyInjection;

use Ozean12\GooglePubSubBundle\Service\Publisher\Publisher;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
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
    const SUBSCRIBER_MANAGER_SERVICE_DEFINITION = 'ozean12_google_pubsub.push_subscriber_manager.service';
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
            $definitions[self::PUBLISHER_SERVICE_DEFINITION.$topic] = (clone $baseDefinition)
                ->replaceArgument(0, $topic)
                ->replaceArgument(1, $pubSubDefinition)
                ->setClass(Publisher::class)
                ->setPublic(true)
                ->addTag(self::TAG_NAME)
            ;
        }

        $subscriberManager = $container->getDefinition(self::SUBSCRIBER_MANAGER_SERVICE_DEFINITION);
        foreach ($config['push_subscriptions'] as $subscriberServiceName) {
            $subscriberManager->addMethodCall('addSubscriber', [new Reference($subscriberServiceName)]);
        }

        $container->setDefinitions($definitions);
    }
}
