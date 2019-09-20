<?php

namespace Vidarl\DummyBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class VidarlDummyExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    public function prepend( ContainerBuilder $container )
    {
        $this->prependConfig($container, __DIR__ . '/../Resources/config/ezpublish.yml', 'ezpublish');
        $this->prependConfig($container, __DIR__ . '/../Resources/config/richtext.yml', 'ezrichtext');
/*        $configFile = __DIR__ . '/../Resources/config/custom_tags.yml';
        $config = Yaml::parse( file_get_contents( $configFile ) );
        $container->prependExtensionConfig( 'ezpublish', $config );
            $container->prependExtensionConfig( 'ezrichtext', $config );
        $container->addResource( new FileResource( $configFile ) );
*/
    }

    public function prependConfig(ContainerBuilder $container, $configFile, $context)
    {
        $config = Yaml::parse( file_get_contents( $configFile ) );
        $container->prependExtensionConfig( $context, $config );
        $container->addResource( new FileResource( $configFile ) );
    }
}
