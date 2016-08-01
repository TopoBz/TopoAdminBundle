<?php

namespace Topo\AdminBundle\Admin;

use FOS\UserBundle\Model\UserInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class AdminUserAdmin extends AbstractAdmin
{
    /**
     * Security access.
     *
     * @var array
     */
    protected $accessMapping = [
        'list' => UserInterface::ROLE_SUPER_ADMIN,
        'create' => UserInterface::ROLE_SUPER_ADMIN,
        'edit' => UserInterface::ROLE_SUPER_ADMIN,
        'delete' => UserInterface::ROLE_SUPER_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function getFormBuilder()
    {
        /** @var UserInterface */
        $adminUser = $this->getSubject();

        $this->formOptions['data_class'] = $this->getClass();

        $options = $this->formOptions;
        $options['validation_groups'] = (!$adminUser || null === $adminUser->getId()) ? 'Registration' : 'Profile';

        $formBuilder = $this->getFormContractor()->getFormBuilder($this->getUniqid(), $options);

        $this->defineFormBuilder($formBuilder);

        return $formBuilder;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list', 'create', 'edit', 'delete']);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureBatchActions($actions)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('username', null, ['label' => 'admin_user.list.username'])
            ->add('email', null, ['label' => 'admin_user.list.email'])
            ->add('enabled', null, ['label' => 'admin_user.list.enabled', 'editable' => true])
            ->add('_action', null, [
                'label' => 'list.action',
                'actions' => ['edit' => [], 'delete' => []],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /** @var UserInterface */
        $adminUser = $this->getSubject();

        $formMapper
            ->add('username', null, [
                'label' => 'admin_user.label.username',
            ])
            ->add('email', null, [
                'label' => 'admin_user.label.email',
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'form.password'],
                'second_options' => ['label' => 'form.password_confirmation'],
                'invalid_message' => 'fos_user.password.mismatch',
                'required' => (!$adminUser || null === $adminUser->getId()),
                'options' => ['translation_domain' => 'FOSUserBundle'],
            ]);
    }
}
