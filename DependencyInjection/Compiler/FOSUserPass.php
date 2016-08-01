<?php

namespace Topo\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FOSUserPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // firewall
        $firewallName = $container->getParameter('fos_user.firewall_name');
        $container->setParameter('topo_admin.admin_user.firewall_name', $firewallName);

        // user manager
        $container->setAlias('topo_admin.admin_user.manager', 'fos_user.user_manager');

        // user provider
        $container->setAlias('topo_admin.admin_user.provider.username', 'fos_user.user_provider.username');
        $container->setAlias('topo_admin.admin_user.provider.username_email', 'fos_user.user_provider.username_email');

        // mailer
        $serviceIdMailer = (string) $container->getAlias('fos_user.mailer');

        $container
            ->getDefinition($serviceIdMailer)
            ->setClass('Topo\\AdminBundle\\Mailer\\AdminUserMailer')
            ->replaceArgument(2, new Reference('twig'));

        $container->setAlias('topo_admin.admin_user.mailer', $serviceIdMailer);
    }
}
