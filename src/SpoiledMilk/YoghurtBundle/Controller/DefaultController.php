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
     * Swaps positions of two elements in array, and returns an array of
     * db entities that need to be persisted
     * 
     * @param array $array
     * @param object $array[$i] 
     * @param sting $dir up or down
     * @return array
     */
    public function swapPositions(&$array, $id, $dir) {
        $ret = array();

        for ($i = 0; $i < count($array); $i++) {
            if ($array[$i]->getId() != $id) {
                continue;
            } else {
                if ($dir == 'up' && $i > 0) {
                    $ret = array(&$array[$i], &$array[$i - 1]);
                } else if ($dir == 'down' && $i < count($array) - 1) {
                    $ret = array(&$array[$i], &$array[$i + 1]);
                }

                break;
            }
        }

        if (count($ret) > 0) {
            $pos0 = $ret[0]->getPosition();
            $pos1 = $ret[1]->getPosition();
            $ret[0]->setPosition($pos1);
            $ret[1]->setPosition($pos0);
        }

        return $ret;
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
