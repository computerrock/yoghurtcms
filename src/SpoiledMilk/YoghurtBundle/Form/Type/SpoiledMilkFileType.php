<?php

namespace SpoiledMilk\YoghurtBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Extension of the standard FileType
 *
 * @author nenadmitic
 */
class SpoiledMilkFileType extends FileType {

    private $yoghurtService;

    public function __construct($yoghurtService) {
        $this->yoghurtService = $yoghurtService;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options) {
        $view->vars = array_replace($view->vars, array(
            'multipart' => true,
            'type' => 'file',
            'value' => ''
        ));

        if ($form->getData()) {
            switch ($this->yoghurtService->getFileMimeType($form->getData())) {
                case 'img':
                    $view->vars['data_img'] = $form->getData();
                    break;
                case 'bin':
                    $view->vars['data_file'] = $form->getData();
                    break;
            }
        }
    }

    public function getParent() {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'smfile';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'compound' => false,
            'data_class' => null,
            'empty_data' => null,
        ));
    }

}