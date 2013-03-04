<?php

namespace SpoiledMilk\YoghurtBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use SpoiledMilk\YoghurtBundle\Form\EventListener\FieldValueTypeSubscriber;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FieldValueType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $subscriber = new FieldValueTypeSubscriber($builder->getFormFactory());
        $builder->setErrorBubbling(false);
        $builder->addEventSubscriber($subscriber);
        
    }

    public function getName() {
        return 'spoiledmilk_yoghurtbundle_fieldvaluetype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'SpoiledMilk\YoghurtBundle\Entity\FieldValue'
        ));
    }
    
}
