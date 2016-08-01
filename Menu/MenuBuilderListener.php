<?php

namespace Topo\AdminBundle\Menu;

use FOS\UserBundle\Model\UserInterface;
use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Event\ConfigureMenuEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class MenuBuilderListener
{
    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Constructor.
     *
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TranslatorInterface           $translator
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        TranslatorInterface $translator
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->translator = $translator;
    }

    /**
     * Handel the admin sidebar menu.
     *
     * @param ConfigureMenuEvent $event
     */
    public function addMenuItems(ConfigureMenuEvent $event)
    {
        /** @var ItemInterface */
        $menu = $event->getMenu();

        $this->addAdministratorMenuItem($menu);
    }

    /**
     * Adds the administrator menu item.
     *
     * @param ItemInterface $menu
     */
    protected function addAdministratorMenuItem(ItemInterface $menu)
    {
        // only super admin can see the menu item
        if ($this->authorizationChecker->isGranted(UserInterface::ROLE_SUPER_ADMIN)) {
            $menu->addChild('admin_user', [
                'label' => $this->translator->trans('sidemenu.admin_user.list', [], 'TopoAdminBundle'),
                'route' => 'admin_app_adminuser_list',
                'attributes' => ['icon' => '<i class="fa fa-users" aria-hidden="true"></i>'],
            ]);
        }
    }
}
