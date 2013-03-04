<?php

namespace SpoiledMilk\YoghurtBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class HelperController extends DefaultController {

    /**
     * @Template()
     */
    public function topMenuAction() {
        $entityTypes = $this->getEntityTypes();

        return array(
            'entityTypes' => $entityTypes,
        );
    }

    /**
     * @Template 
     */
    public function leftMenuAction() {
        $entityTypes = $this->getEntityTypes();
        $requestUrl = $this->getRequest()->getRequestUri();
        $activeIndex = -1;

        if (stripos($requestUrl, '/admin/user') === 0) {

            $activeIndex = 'user';
            
        } else if (stripos($requestUrl, '/admin/vocabulary') === 0
                || stripos($requestUrl, '/admin/term') === 0) {

            $activeIndex = 'vocabulary';
            
        } else if (stripos($requestUrl, '/admin/entitytype') === 0) {

            if (stripos($requestUrl, '/admin/entitytype/show') === 0) {
                
                $typeId = explode('/', $requestUrl);
                $activeIndex = $typeId[count($typeId) - 1];
                
            } else if (stripos($requestUrl, '/admin/entitytype/edit') === 0
                    || stripos($requestUrl, '/admin/entitytype/new') === 0
                    || $requestUrl == '/admin/entitytype/') {
                
                $activeIndex = 'entityType';
                
            }
            
        } else if (stripos($requestUrl, '/admin/entity/new') === 0) {
            
            $typeId = explode('/', $requestUrl);
            $activeIndex = $typeId[count($typeId) - 1];
            
        } else if (stripos($requestUrl,'/admin/entity/edit') === 0) {
            // get the entity's id
            $entityId = explode('/', $requestUrl);
            $entityId = $entityId[count($entityId) - 1];

            // get the entity
            $entity = $this->getDoctrine()
                    ->getRepository('SpoiledMilkYoghurtBundle:Entity')
                    ->find($entityId);

            if ($entity) {
                $activeIndex = $entity->getEntityType()->getId();
            }
            
        } else if (stripos($requestUrl, '/admin') === 0) {
            $activeIndex = 'publishing';
        }

        return array(
            'entityTypes' => $entityTypes,
            'activeIndex' => "$activeIndex",
        );
    }
    
    /**
     * @Template
     * @param \SpoiledMilk\YoghurtBundle\Entity\Entity $entity
     * @return array
     * @throws \Exception 
     */
    public function statusButtonAction($entity) {
        $back = $this->getRequest()->getUri();
        
        switch ($entity->getStatus()) {
            case \SpoiledMilk\YoghurtBundle\Entity\Entity::STATUS_ENABLED:
                $buttons = array(
                    $this->getEnableButton($entity, $back),
                    $this->getTestButton($entity, $back),
                    $this->getDisableButton($entity, $back)
                );
                break;
            case \SpoiledMilk\YoghurtBundle\Entity\Entity::STATUS_TEST:
                $buttons = array(
                    $this->getTestButton($entity, $back),
                    $this->getEnableButton($entity, $back),
                    $this->getDisableButton($entity, $back)
                );
                break;
            case \SpoiledMilk\YoghurtBundle\Entity\Entity::STATUS_DISABLED:
                $buttons = array(
                    $this->getDisableButton($entity, $back),
                    $this->getEnableButton($entity, $back),
                    $this->getTestButton($entity, $back)
                );
                break;
            default:
                throw new \Exception('Unexpected status value: ' . $entity->getStatus());
        }

        return array('buttons' => $buttons);
    }

    private function getEnableButton($entity, $back = null) {
        $btnHref = $this->generateUrl('yoghurt_entity_status', array(
            'id' => $entity->getId(),
            'status' => \SpoiledMilk\YoghurtBundle\Entity\Entity::STATUS_ENABLED));

        return array(
            'href' => ($back ? $btnHref . "?back=$back" : $btnHref),
            'icon' => 'icon-ok',
            'changeText' => 'Enable',
            'text' => 'Enabled',
            'class' => 'btn-success',
            'title' => 'This entity is publicly available'
        );
    }

    private function getDisableButton($entity, $back = null) {
        $btnHref = $this->generateUrl('yoghurt_entity_status', array(
            'id' => $entity->getId(),
            'status' => \SpoiledMilk\YoghurtBundle\Entity\Entity::STATUS_DISABLED));

        return array(
            'href' => ($back ? $btnHref . "?back=$back" : $btnHref),
            'icon' => 'icon-remove',
            'changeText' => 'Disable',
            'text' => 'Disabled',
            'class' => 'btn-warning',
            'title' => 'This entity can\'t be seen by anybody'
        );
    }

    private function getTestButton($entity, $back = null) {
        $btnHref = $this->generateUrl('yoghurt_entity_status', array(
            'id' => $entity->getId(),
            'status' => \SpoiledMilk\YoghurtBundle\Entity\Entity::STATUS_TEST));

        return array(
            'href' => ($back ? $btnHref . "?back=$back" : $btnHref),
            'icon' => 'icon-pencil',
            'changeText' => 'Test',
            'text' => 'Testing',
            'class' => 'btn-info',
            'title' => 'This can be seen only by a select group of people'
        );
    }

}