<?php

namespace SpoiledMilk\YoghurtBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FieldType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
                ->add('name', 'text', array(
                    'required' => true))
                ->add('label', 'text', array(
                    'attr' => array(
                        'placeholder' => 'Leave blank for default'
                        )))
                ->add('field_type', 'entity', array(
                    'required' => true,
                    'class' => 'SpoiledMilk\YoghurtBundle\Entity\FieldType'
                ))
                ->add('position', 'number', array(
                    'required' => true,
                    'precision' => 0,))
                ->add('repeating', 'checkbox', array(
                    'required' => false,))
                ->add('description', 'textarea', array(
                    'required' => false,
                    'attr' => array(
                        'rows' => 3
                    )
                ))
        ;
    }

    public function getName() {
        return 'spoiledmilk_yoghurtbundle_fieldtype';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'SpoiledMilk\YoghurtBundle\Entity\Field'
        ));
    }

}