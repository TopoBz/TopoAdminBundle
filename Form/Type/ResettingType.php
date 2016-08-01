<?php

namespace Topo\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResettingType extends AbstractType
{
    /**
     * @var string
     */
    protected $adminUserClass;

    /**
     * @var array|null
     */
    private $validationGroups;

    /**
     * Constructor.
     *
     * @param string     $adminUserClass
     * @param array|null $validationGroups
     */
    public function __construct($adminUserClass, array $validationGroups = null)
    {
        $this->adminUserClass = $adminUserClass;
        $this->validationGroups = $validationGroups;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'options' => ['translation_domain' => 'FOSUserBundle'],
            'first_options' => [
                'label' => 'form.new_password',
                'label_attr' => ['class' => 'sr-only'],
                'attr' => ['placeholder' => 'form.new_password'],
            ],
            'second_options' => [
                'label' => 'form.new_password_confirmation',
                'label_attr' => ['class' => 'sr-only'],
                'attr' => ['placeholder' => 'form.new_password_confirmation'],
            ],
            'invalid_message' => 'fos_user.password.mismatch',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->adminUserClass,
            'csrf_token_id' => 'resetting',
            'validation_groups' => $this->validationGroups,
        ]);
    }
}
