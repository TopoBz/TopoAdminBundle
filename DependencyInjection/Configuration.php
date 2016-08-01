<?php

namespace Topo\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @var boole
     */
    private $useFOSUserBundle;

    /**
     * Constructor.
     *
     * @param bool $useFOSUserBundle Checks if the admin user is extended by FOSUserBundle
     */
    public function __construct($useFOSUserBundle)
    {
        $this->useFOSUserBundle = (bool) $useFOSUserBundle;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('topo_admin');

        $supportedDrivers = ['orm'];

        if (!$this->useFOSUserBundle) {
            die("@TODO Topo\AdminBundle\DependencyInjection\Configuration");
        }

        $rootNode
            ->children()
                // ->scalarNode('db_driver')
                //     ->validate()
                //         ->ifNotInArray($supportedDrivers)
                //         ->thenInvalid('The driver %s is not supported. Please choose one of ' . json_encode($supportedDrivers))
                //     ->end()
                //     ->cannotBeOverwritten()
                //     ->isRequired()
                //     ->cannotBeEmpty()
                // ->end()
                // ->scalarNode('firewall_name')->isRequired()->cannotBeEmpty()->end()
                // ->scalarNode('admin_user_class')->isRequired()->cannotBeEmpty()->end()
                // ->scalarNode('model_manager_name')->defaultNull()->end()
                ->scalarNode('sonata_admin_user_class')->defaultValue('Topo\\AdminBundle\\Admin\\AdminUserAdmin')->cannotBeEmpty()->end()
                ->arrayNode('assets')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('stylesheets')
                                ->defaultValue([])->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('javascripts')
                                ->defaultValue([])->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
