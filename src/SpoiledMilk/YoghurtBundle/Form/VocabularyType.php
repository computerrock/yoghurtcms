<?php

namespace SpoiledMilk\YoghurtBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VocabularyType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('name', 'text')
                ->add('slug', 'text')
                ->add('entityTypeVocabularies', 'collection', array(
                    'error_bubbling' => true,
                    'required' => false,
                    'label' => 'Vocabularies',
                    'type' => new EntityTypeVocabularyType()
                ))
        ;
    }

    public function getName() {
        return 'spoiledmilk_yoghurtbundle_vocabularytype';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'SpoiledMilk\YoghurtBundle\Entity\Vocabulary',
            'cascade_validation' => true,
        ));
    }

}
