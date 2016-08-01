<?php

namespace Topo\AdminBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Topo\AdminBundle\DependencyInjection\Compiler\AddAssetsSonataAdminPoolPass;
use Topo\AdminBundle\DependencyInjection\Compiler\FOSUserPass;
use Topo\AdminBundle\DependencyInjection\TopoAdminExtension;

class TopoAdminBundle extends Bundle
{
    /**
     * @var string
     */
    private $parent;

    /**
     * Constructor.
     *
     * @param bool $parent Checks if the bundle extends FOSUserBundle
     */
    public function __construct($parent = null)
    {
        if ($parent && 'FOSUserBundle' !== $parent) {
            throw new \InvalidArgumentException('The TopoAdminBundle only can be extends by FOSUserBundle.');
        }

        $this->parent = $parent;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        if ($this->parent) {
            $container->addCompilerPass(new FOSUserPass());
        }

        $container->addCompilerPass(new AddAssetsSonataAdminPoolPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new TopoAdminExtension((bool) $this->parent);
        }

        return $this->extension;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }
}
