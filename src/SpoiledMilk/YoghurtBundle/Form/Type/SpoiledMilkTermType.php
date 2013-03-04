<?php

namespace SpoiledMilk\YoghurtBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use SpoiledMilk\YoghurtBundle\Form\DataTransformer\TermsTransformer;

/**
 * Extension of the standard TextType
 *
 * @author nenadmitic
 */
class SpoiledMilkTermType extends TextType {

    private $entityManager;

    function __construct($entityManager) {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $transformer = new TermsTransformer($this->entityManager);
        $builder->appendClientTransformer($transformer);

        if (isset($options['vocabularyIds'])) {
            $terms = $this->entityManager
                    ->getRepository('SpoiledMilkYoghurtBundle:Term')
                    ->fetchFromVocabularies($options['vocabularyIds']);
        } else {
            $terms = $this->entityManager
                    ->getRepository('SpoiledMilkYoghurtBundle:Term')
                    ->findAll();
        }

        $builder->setAttribute('terms', $terms);
    }

    public function buildView(FormView $view, FormInterface $form, array $options) {
        parent::buildView($view, $form);
        $view->set('terms', $form->getAttribute('terms'));
    }

    public function getParent() {
        return 'text';
    }

    public function getName() {
        return 'smterm';
    }
    
    public function getDefaultOptions(array $options) {
        $ret = parent::getDefaultOptions($options);
        $ret['vocabularyIds'] = array();
        return $ret;
    }

}