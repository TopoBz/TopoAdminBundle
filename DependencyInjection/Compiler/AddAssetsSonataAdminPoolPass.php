<?php

namespace Topo\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AddAssetsSonataAdminPoolPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sonata.admin.pool')) {
            return;
        }

        $def = $container->getDefinition('sonata.admin.pool');
        $arguments = $def->getArgument(3);

        // default Topo assets
        $assets = [
            'stylesheets' => ['bundles/topoadmin/css/admin.css'],
            'javascripts' => [],
        ];

        // check if the parameter exists
        if ($container->hasParameter('topo_admin.assets')) {
            $assets = array_merge_recursive($assets, $container->getParameter('topo_admin.assets'));
        }

        $arguments = array_merge_recursive($arguments, $assets);
        $def->replaceArgument(3, $arguments);
    }
}
