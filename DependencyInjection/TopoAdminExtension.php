<?php

namespace Topo\AdminBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class TopoAdminExtension extends Extension
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
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration($this->useFOSUserBundle);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        //$loader->load('doctrine.xml');
        $loader->load('menu.xml');

        if ($this->useFOSUserBundle) {
            $container->setParameter('topo_admin.admin_user.class', $container->getParameter('fos_user.model.user.class'));
            $container->setParameter('topo_admin.admin_user.manager_type', $container->getParameter('fos_user.storage'));
        }

        $this->registerSonataAdminUserService($config, $container);
        $this->addAssetsParameters($config['assets'], $container);

        // @TODO useFOSUserBundle?
        $this->registerAdminUserResettingFormType($container);
    }

    /**
     * Register the sonata admin service for the admin user.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function registerSonataAdminUserService(array $config, ContainerBuilder $container)
    {
        $adminUserClass = $container->getParameter('topo_admin.admin_user.class');
        $managerType = $container->getParameter('topo_admin.admin_user.manager_type');

        $def = new Definition($config['sonata_admin_user_class']);
        $def->setArguments([null, $adminUserClass, null, 'SonataAdminBundle:CRUD']);
        $def->addMethodCall('setTranslationDomain', ['TopoAdminBundle']);
        $def->addTag('sonata.admin', ['manager_type' => $managerType, 'show_in_dashboard' => false]);

        $container->setDefinition('topo_admin.admin_user', $def);
        $container->setAlias('app.admin.admin_user', 'topo_admin.admin_user');
    }

    /**
     * Register the admin user form type.
     *
     * @param ContainerBuilder $container
     */
    private function registerAdminUserResettingFormType(ContainerBuilder $container)
    {
        $def = new Definition('Topo\\AdminBundle\\Form\\Type\\ResettingType');
        $def->setArguments([
            $container->getParameter('fos_user.model.user.class'),
            $container->getParameter('fos_user.resetting.form.validation_groups'),
        ]);
        $def->addTag('form.type');

        $container->setDefinition('topo_admin.admin_user.resetting.form.type', $def);
    }

    /**
     * Add new admin assets if is not empty, we use this method to not owerride
     * the sonata assets configuration.
     *
     * @param array            $assets
     * @param ContainerBuilder $container
     */
    private function addAssetsParameters(array $assets, ContainerBuilder $container)
    {
        if (!empty($assets['stylesheets']) || !empty($assets['javascripts'])) {
            $container->setParameter('topo_admin.assets', $assets);
        }
    }
}
