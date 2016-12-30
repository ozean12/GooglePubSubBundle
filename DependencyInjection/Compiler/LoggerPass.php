<?php

namespace Ozean12\GooglePubSubBundle\DependencyInjection\Compiler;

use Ozean12\GooglePubSubBundle\DependencyInjection\Ozean12GooglePubSubExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class LoggerPass
 */
class LoggerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getExtensionConfig('ozean12_google_pub_sub');
        $loggerChannel = isset($config[0]['logger_channel']) ? $config[0]['logger_channel'] : null;
        $loggerId = 'monolog.logger.'.$loggerChannel;

        if (is_null($loggerChannel)) {
            return;
        }

        foreach ($container->findTaggedServiceIds(Ozean12GooglePubSubExtension::TAG_NAME) as $taggedServiceId => $tags) {
            $container
                ->getDefinition($taggedServiceId)
                ->addMethodCall('setLogger', [new Reference($loggerId)])
            ;
        }
    }
}
