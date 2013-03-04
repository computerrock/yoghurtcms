<?php

namespace SpoiledMilk\YoghurtBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class EntityType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $hasVocabularies = false;
        $termsRequired = false;
        $vocabularyIds = null;

        if ($options['entityTypeVocabularies']) {
            $hasVocabularies = true;
            $vocabularyIds = array();

            foreach ($options['entityTypeVocabularies'] as $etv) {
                $termsRequired = $termsRequired || $etv->getMandatory();
                $vocabularyIds[] = $etv->getVocabulary()->getId();
            }
        }

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
                ));

        if ($hasVocabularies) {
            $builder->add('terms', 'entity', array(
                'required' => $termsRequired,
                'class' => 'SpoiledMilkYoghurtBundle:Term',
                'multiple' => true,
                'property' => 'indentedTerm',
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er) use ($vocabularyIds) {
                    $qb = $er->createQueryBuilder('t');
                    if ($vocabularyIds) {
                        $qb->add('where', $qb->expr()->in('t.vocabulary', $vocabularyIds));
                    }
                    return $qb;
                },
            ));
        }

        $builder->add('fieldValues', 'collection', array(
            'type' => new FieldValueType()));
    }

    public function getName() {
        return 'spoiledmilk_yoghurtbundle_entitytype';
    }

    public function getDefaultOptions(array $options) {
        $ret = parent::getDefaultOptions($options);
        $ret['entityTypeVocabularies'] = array();
        return $ret;
    }

}
