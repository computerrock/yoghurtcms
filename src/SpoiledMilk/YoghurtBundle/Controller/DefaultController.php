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
    
    /**
     * @param integer|string|\SpoiledMilk\YoghurtBundle\Entity\EntityType $entityType
     * @return integer
     */
    public function getMaxPositionForEntityType($entityType) {
        $dql = 'select max(e.position) from SpoiledMilkYoghurtBundle:Entity e join e.entityType et';

        try {
            if ($entityType instanceof \SpoiledMilk\YoghurtBundle\Entity\EntityType) {
                $dql .= ' where et.id = :etid';
                $res = $this->getDoctrine()->getManager()
                        ->createQuery($dql)
                        ->setParameter('etid', $entityType->getId())
                        ->getSingleScalarResult();
            } else if (is_numeric($entityType)) {
                $dql .= ' where et.id = :etid';
                $res = $this->getDoctrine()->getManager()
                        ->createQuery($dql)
                        ->setParameter('etid', $entityType)
                        ->getSingleScalarResult();
            } else {
                $dql .= ' where et.slug = :slug';
                $res = $this->getDoctrine()->getManager()
                        ->createQuery($dql)
                        ->setParameter('slug', $entityType)
                        ->getSingleScalarResult();
            }
        } catch (\Doctrine\ORM\Query\QueryException $ex) {
            $res = 0;
        }
        
        return $res;
    }

}
