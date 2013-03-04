<?php

namespace SpoiledMilk\YoghurtBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text')
            ->add('password', 'text')
            ->add('role', 'choice', array(
                'label' => 'Group',
                'choices' => array(
                    'ROLE_USER' => 'User',
                    'ROLE_ADMIN' => 'Administrator',
                ),
            ))
            ->add('isActive', 'checkbox', array(
                'required' => false,
                'label' => 'Account active',
            ))
            ->add('email', 'email', array(
                'label' => 'e-mail'
            ))
            ->add('newPassword', 'text', array(
                'label' => 'Change password',
                'required' => false,
            ))
        ;
    }

    public function getName()
    {
        return 'spoiledmilk_yoghurtbundle_usertype';
    }
}
