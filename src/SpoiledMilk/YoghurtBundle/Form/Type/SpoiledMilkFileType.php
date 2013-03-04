<?php

namespace SpoiledMilk\YoghurtBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

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
        $view
                ->set('multipart', true)
                ->set('type', 'file')
                ->set('value', '')
        ;

        if ($form->getData()) {
            switch ($this->yoghurtService->getFileMimeType($form->getData())) {
                case 'img':
                    $view->set('data_img', $form->getData());
                    break;
                case 'bin':
                    $view->set('data_file', $form->getData());
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

}

?>
