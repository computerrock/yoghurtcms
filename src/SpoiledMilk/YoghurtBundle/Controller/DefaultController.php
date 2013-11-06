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

    public function getAllErrors(\Symfony\Component\Form\Form $form) {
        $errors = array();
        $formErrors = $this->getFormErrors($form);

        if ($formErrors) {
            $errors['form'] = $formErrors;
        }

        foreach ($form->all() as $child) {
            $childErrors = $this->getFormErrors($child);

            if ($childErrors) {
                $errors[ucfirst($child->getName())] = $childErrors;
            }
        }

        return $errors;
    }

    /**
     * Checks weather the current user has ROLE_ADMIN
     *
     * @return boolean
     */
    public function isAdmin() {
        $user = $this->getUser();

        if (!$user) {
            return false;
        }

        return in_array('ROLE_ADMIN', $user->getRoles());
    }

}