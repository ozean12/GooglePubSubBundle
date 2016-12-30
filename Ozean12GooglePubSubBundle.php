<?php

namespace Ozean12\GooglePubSubBundle;

use Ozean12\GooglePubSubBundle\DependencyInjection\Compiler\LoggerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class Ozean12GooglePubSubBundle
 */
class Ozean12GooglePubSubBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new LoggerPass());
    }
}
