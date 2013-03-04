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
                ->add('metaValue', 'text', array(
                    'label' => 'Setting value'
                ))
        ;
    }

    public function getName() {
        return 'spoiledmilk_yoghurtbundle_fieldmetatype';
    }

}
