<?php

namespace SpoiledMilk\YoghurtBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

    public function getEntityTypes($id = null) {
        $repo = $this->getDoctrine()->getRepository('SpoiledMilkYoghurtBundle:EntityType');

        if ($id) {
            return $repo->fetchByIdSorted($id);
        } else {
            return $repo->fetchAllSorted();
        }
    }

    public function getFormErrors(\Symfony\Component\Form\Form $form) {
        $errors = array();

        foreach ($form->getErrors() as $error) {
            $template = $error->getMessageTemplate();
            $params = $error->getMessageParameters();

            foreach ($params as $pkey => $pval) {
                $template = str_replace($pkey, $pval, $template);
            }

            $errors[] = $template;
        }

        return $errors;
    }

}