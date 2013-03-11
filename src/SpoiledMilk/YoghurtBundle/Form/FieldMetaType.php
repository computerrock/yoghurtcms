<?php

namespace SpoiledMilk\YoghurtBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class FieldMetaType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('metaKey', 'text', array(
                    'label' => 'Setting name'
                ))
                ->add('metaValue', 'textarea', array(
                    'label' => 'Setting value',
                    'attr' => array(
                        'class' => 'input-xxlarge',
                        'style' => 'height: 18px'
                    )
                ))
        ;
    }

    public function getName() {
        return 'spoiledmilk_yoghurtbundle_fieldmetatype';
    }

}
