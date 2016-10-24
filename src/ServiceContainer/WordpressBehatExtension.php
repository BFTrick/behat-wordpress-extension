<?php
namespace PaulGibbs\WordpressBehatExtension\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension,
    Behat\Testwork\ServiceContainer\Extension as ExtensionInterface,
    Behat\Testwork\ServiceContainer\ExtensionManager;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Definition;

/**
 * WordpressBehatExtension is an integration layer between Behat and WordPress.
 */
class WordpressBehatExtension implements ExtensionInterface
{
    /**
     * Returns the extension config key.
     *
     * @return string
     */
    public function getConfigKey()
    {
        return 'wordpress';
    }

    /**
     * Initializes extension.
     *
     * @param ExtensionManager $extensionManager
     */
    public function initialize(ExtensionManager $extensionManager)
    {
    }

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
    }

    /**
     * Set up configuration for the extension.
     *
     * @param ArrayNodeDefinition $builder
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->scalarNode('default_driver')
                    ->defaultValue('wpcli')
                ->end()
                ->arrayNode('wpcli')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('alias')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * Loads extension services into temporary container.
     *
     * @param ContainerBuilder $container
     * @param array            $config
     */
    public function load(ContainerBuilder $container, array $config)
    {
        // http://symfony.com/doc/current/service_container.html
        $container->setDefinition('wordpress.driver.wpcli', new Definition(
            'PaulGibbs\WordpressBehatExtension\Driver\WpcliDriver',
            array('%wordpress.parameters.wpcli.alias%')
        ));
        $definition->addTag(ContextExtension::INITIALIZER_TAG, array('priority' => 0));

        // $definition = $container->getDefinition('wordpress.driver.wpcli');
        // $definition->addMethodCall('setArguments', array($config['drush']['global_options']));

        $definition = new Definition(
            'PaulGibbs\WordpressBehatExtension\Context\Initializer\WordpressAwareInitializer',
            array(
                $container->get('wordpress.driver.wpcli'),
                '%wordpress.parameters%'
            )
        );
        $definition->addTag(ContextExtension::INITIALIZER_TAG, array('priority' => 0));
        $container->setDefinition('PaulGibbs.wordpress.initializer', $definition);

        $container->setParameter('wordpress.parameters', $config);
        $container->setParameter('wordpress.default_driver', $config['default_driver']);

        $this->loadWpcliDriverSettings($container, $config);
    }

    /**
     * Loads settings for the WP-CLI driver.
     *
     * @param ContainerBuilder $container
     * @param array            $config
     */
    protected function loadWpcliDriverSettings(ContainerBuilder $container, array $config)
    {
        if (empty($config['wpcli']['alias'])) {
            die('WP-CLI driver requires `alias` set.');
        }

        $container->setParameter('wordpress.wpcli.alias', $config['wpcli']['alias']);
    }
}
