<?php

namespace SpoiledMilk\YoghurtBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TermType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('term', 'text')
                ->add('slug', 'text')
                ->add('parent', 'entity', array(
                    'required' => false,
                    'label' => 'Parent',
                    'empty_value' => 'Chose parent term',
                    'class' => 'SpoiledMilkYoghurtBundle:Term',
                    'property' => 'term',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($options) {
                        $builder = $er->createQueryBuilder('t')
                                ->innerJoin('t.vocabulary', 'v');

                        $builder->add('where', $builder->expr()->notIn('t', $options['termTreeIds']))
                                ->andWhere('v.id = :vid')
                                ->setParameter('vid', $options['vocabularyId'])
                        ;
                        
                        return $builder;
                    }
                ))
        ;
    }

    public function getName() {
        return 'spoiledmilk_yoghurtbundle_termtype';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'SpoiledMilk\YoghurtBundle\Entity\Term',
            'vocabularyId' => 0,
            'termTreeIds' => array(0)
        ));
    }

}
