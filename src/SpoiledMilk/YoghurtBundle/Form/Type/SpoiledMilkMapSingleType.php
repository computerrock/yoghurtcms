<?php

namespace SpoiledMilk\YoghurtBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SpoiledMilkMapSingleType extends TextType {

    public function getParent() {
        return 'text';
    }

    public function getName() {
        return 'smMapSingle';
    }

}
