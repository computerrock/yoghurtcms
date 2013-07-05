<?php

namespace SpoiledMilk\YoghurtBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EntityType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('title')
                ->add('slug', 'text', array('required' => false))
                ->add('position', 'number', array('required' => false))
                ->add('status', 'choice', array(
                    'label' => 'Status',
                    'empty_value' => false,
                    'choices' => array(
                        \SpoiledMilk\YoghurtBundle\Entity\Entity::STATUS_DISABLED => 'Disabled',
                        \SpoiledMilk\YoghurtBundle\Entity\Entity::STATUS_ENABLED => 'Enabled',
                        \SpoiledMilk\YoghurtBundle\Entity\Entity::STATUS_TEST => 'Test'
                    )
                ))
                ->add('vocabularyTerms', 'hidden', array('mapped' => false))
                ->add('fieldValues', 'collection', array(
                    'type' => new FieldValueType()));
    }

    public function getName() {
        return 'spoiledmilk_yoghurtbundle_entitytype';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'SpoiledMilk\YoghurtBundle\Entity\Entity',
            'cascade_validation' => true,
            'entityTypeVocabularies' => array()
        ));
    }

}
