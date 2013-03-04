<?php

namespace SpoiledMilk\YoghurtBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EntityTypeVocabularyType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('vocabulary', 'entity', array(
                    'error_bubbling' => true,
                    'class' => 'SpoiledMilkYoghurtBundle:Vocabulary',
                    'property' => 'name',
                ))
                ->add('entityType', 'entity', array(
                    'error_bubbling' => true,
                    'class' => 'SpoiledMilkYoghurtBundle:EntityType',
                    'property' => 'name',
                ))
                ->add('mandatory', 'checkbox')
        ;
    }

    public function getName() {
        return 'spoiledmilk_yoghurtbundle_entitytypevocabularytype';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'SpoiledMilk\YoghurtBundle\Entity\EntityTypeVocabulary'
        ));
    }

}
