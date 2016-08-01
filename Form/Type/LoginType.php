<?php

namespace Topo\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // default symfony parameter
            ->add('_username', TextType::class, [
                'label' => 'security.login.username',
                'label_attr' => ['class' => 'sr-only'],
                'attr' => ['placeholder' => 'security.login.username'],
            ])
            // default symfony parameter
            ->add('_password', PasswordType::class, [
                'label' => 'security.login.password',
                'label_attr' => ['class' => 'sr-only'],
                'attr' => ['placeholder' => 'security.login.password'],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('translation_domain', 'FOSUserBundle');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return null;
    }
}
