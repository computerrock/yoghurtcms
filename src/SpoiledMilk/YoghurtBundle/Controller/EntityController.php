<?php

namespace SpoiledMilk\YoghurtBundle\Controller;

use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SpoiledMilk\YoghurtBundle\Entity as Entity;
use SpoiledMilk\YoghurtBundle\Form as Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Entity controller.
 *
 * @Route("/entity")
 */
class EntityController extends DefaultController {

    /**
     * Displays a form to create a new Entity.
     *
     * @Route("/new/{type_id}", name="yoghurt_entity_new")
     * @Template()
     */
    public function newAction($type_id) {
        $em = $this->getDoctrine()->getManager();
        $entityType = $em->getRepository('SpoiledMilkYoghurtBundle:EntityType')->find($type_id);

        if (!$entityType) {
            throw $this->createNotFoundException('Entity Type not found!');
        }

        $entity = new Entity\Entity();
        $entity->setEntityType($entityType);
        $this->addFieldValues($entity);
        $form = $this->createForm(new Form\EntityType(), $entity);
        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );
    }

    /**
     * Creates a new Entity.
     *
     * @Route("/create/{type_id}/{next}", name="yoghurt_entity_create", defaults={"next" = false})
     * @Method("post")
     * @Template("SpoiledMilkYoghurtBundle:Entity:new.html.twig")
     */
    public function createAction($type_id, $next) {
        $em = $this->getDoctrine()->getManager();
        $entityType = $em->getRepository('SpoiledMilkYoghurtBundle:EntityType')->find($type_id);

        if (!$entityType) {
            throw $this->createNotFoundException('Entity Type not found!');
        }

        $entity = new Entity\Entity();
        $entity->setEntityType($entityType);
        $this->addFieldValues($entity);
        $form = $this->createForm(new Form\EntityType(), $entity);
        $rawData = $this->getRequest()->get($form->getName());
        $this->setTerms($entity, $rawData['vocabularyTerms']);
        $form->bind($this->getRequest());

        if ($form->isValid()) {

            if (!$entity->getSlug()) {
                $entity->setSlug($em->getRepository('SpoiledMilkYoghurtBundle:Entity')->generateSlug($entity->getTitle()));
            }

            if (!$entity->getPosition()) {
                $entity->setPosition($em->getRepository('SpoiledMilkYoghurtBundle:Entity')->getMaxPositionForEntityType($entityType) + 1);
            }

            $this->uploadFiles(array(), $entity);
            $em->persist($entity);
            $em->flush();
            $this->getRequest()->getSession()->getFlashBag()->add('success', 'New ' . $entity->getEntityType()->getName() . ' successfully created.');

            if ($next)
                return $this->redirect($this->generateUrl('yoghurt_entity_new', array('type_id' => $type_id)));
            else
                return $this->redirect($this->generateUrl('yoghurt_entity_edit', array('id' => $entity->getId())));
        } else {
            $this->getRequest()->getSession()->getFlashBag()->add('error', 'The form containes errors, changes were not saved.');
            $errors = $this->getFormErrors($form);
            for ($i = 0; $i < sizeof($errors); $i++) {
                $this->getRequest()->getSession()->getFlashBag()->add('error ' . $i, $errors[$i]);
            }
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Entity.
     *
     * @Route("/edit/{id}", name="yoghurt_entity_edit")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SpoiledMilkYoghurtBundle:Entity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Entity.');
        }

        $this->addFieldValues($entity);
        $repeatingForms = $this->getRepeatingForms($entity);
        $editForm = $this->createForm(new Form\EntityType(), $entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'repeating_forms' => $repeatingForms,
        );
    }

    /**
     * Edits an existing Entity.
     *
     * @Route("/{id}/update", name="yoghurt_entity_update")
     * @Method("post")
     * @Template("SpoiledMilkYoghurtBundle:Entity:edit.html.twig")
     */
    public function updateAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SpoiledMilkYoghurtBundle:Entity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Entity.');
        }

        $this->addFieldValues($entity);
        $existingFiles = $this->getFiles($entity);
        $editForm = $this->createForm(new Form\EntityType(), $entity);
        $request = $this->getRequest();
        $rawData = $request->get($editForm->getName());
        $this->setTerms($entity, $rawData['vocabularyTerms']);
        $editForm->bind($request);

        if ($editForm->isValid()) {

            if (!$entity->getSlug()) {
                $entity->setSlug($em->getRepository('SpoiledMilkYoghurtBundle:Entity')->generateSlug($entity->getTitle()));
            }

            if (!$entity->getPosition()) {
                $entity->setPosition($em->getRepository('SpoiledMilkYoghurtBundle:Entity')->getMaxPositionForEntityType($entity->getEntityType()) + 1);
            }

            $entity->setModified(new DateTime());
            $this->uploadFiles($existingFiles, $entity);
            $em->persist($entity);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', $entity->getEntityType()->getName() . ' successfully updated.');
            return $this->redirect($this->generateUrl('yoghurt_entity_edit', array('id' => $id)));
        } else {
            $this->getRequest()->getSession()->getFlashBag()->add('error', 'The form containes errors, changes were not saved.');
            $errors = $this->getFormErrors($editForm);
            for ($i = 0; $i < sizeof($errors); $i++) {
                $this->getRequest()->getSession()->getFlashBag()->add('error ' . $i, $errors[$i]);
            }
        }

        $repeatingForms = $this->getRepeatingForms($entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'repeating_forms' => $repeatingForms,
        );
    }

    /**
     * Deletes a Entity.
     * @Route("/{id}/delete", name="yoghurt_entity_delete")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SpoiledMilkYoghurtBundle:Entity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Entity.');
        }

        $em->remove($entity);
        $em->flush();
        $this->deleteFiles($entity);
        return $this->redirect($this->generateUrl('yoghurt_entitytype_show', array('id' => $entity->getEntityType()->getId())));
    }

    /**
     * Moves the Entity up or down by one position
     *
     * @Route("/order/{id}/{direction}",
     *   name="yoghurt_entity_order",
     *   requirements={"direction" = "up|down"})
     */
    public function orderEntityAction($id, $direction) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SpoiledMilkYoghurtBundle:Entity');
        $entity = $repo->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Entity.');
        }

        if ($direction == 'up')
            $repo->moveUp($entity);
        else
            $repo->moveDown($entity);

        // Return to page the user started from
        $page = $this->getRequest()->get('page');
        $queryParams = $this->getRequest()->query->all();
        $query = '';

        foreach ($queryParams as $key => $val) {
            if (stripos($key, 'cb-') === 0)
                continue;
            else if ($key == 'operation')
                continue;
            else if ($key == 'page')
                continue;

            if ($query)
                $query .= '&';
            $query .= $key . '=' . $val;
        }

        $url = $this->generateUrl('yoghurt_entitytype_show', array('page' => $page, 'id' => $entity->getEntityType()->getId()));

        if ($query)
            $url .= '?' . $query;

        return $this->redirect($url);
    }

    /**
     * @Route("/{entityId}/addFieldValue/{fieldId}", name="yoghurt_entity_addFieldValue")
     * @Method("post")
     */
    public function addFieldValueAction($entityId, $fieldId) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SpoiledMilkYoghurtBundle:Entity')->find($entityId);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Entity.');
        }

        $field = $em->getRepository('SpoiledMilkYoghurtBundle:Field')->find($fieldId);

        if (!$field) {
            throw $this->createNotFoundException('Unable to find Field.');
        }

        $className = 'SpoiledMilk\YoghurtBundle\Entity\\' . $field->getFieldType()->getClassName();
        $fieldValue = new $className;
        $fieldValue->setEntity($entity);
        $fieldValue->setField($field);
        $form = $this->createForm(new Form\FieldValueType(), $fieldValue);
        $form->bind($this->getRequest());

        if ($form->isValid()) {

            if ($fieldValue instanceof Entity\FileValue && $fieldValue->getValue() != null) {
                $yoghurtService = $this->get('yoghurt_service');
                $fileName = $yoghurtService->uploadFile($fieldValue->getValue(), uniqid());
                $fieldValue->setValue($fileName);
            }

            $entity->setModified(new DateTime());
            $em->persist($fieldValue);
            $em->persist($entity);
            $em->flush();
            $this->getRequest()->getSession()->getFlashBag()->add('success', 'New value successfully added.');
        } else {
            $errors = $this->getFormErrors($form);
            for ($i = 0; $i < sizeof($errors); $i++) {
                $this->getRequest()->getSession()->getFlashBag()->add('error ' . $i, $errors[$i]);
            }
        }

        return $this->redirect($this->generateUrl('yoghurt_entity_edit', array('id' => $entityId)));
    }

    /**
     * @Route("/{entityId}/removeFieldValue/{fieldValueId}", name="yoghurt_entity_removeFieldValue")
     */
    public function removeFieldValue($entityId, $fieldValueId) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SpoiledMilkYoghurtBundle:Entity')->find($entityId);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Entity.');
        }

        $fieldValue = $em->getRepository('SpoiledMilkYoghurtBundle:FieldValue')->find($fieldValueId);

        if (!$fieldValue) {
            throw $this->createNotFoundException('Unable to find Field.');
        }

        $em->remove($fieldValue);

        if ($fieldValue instanceof Entity\FileValue) {
            $this->get('yoghurt_service')->deleteFile($fieldValue->getValue());
        }

        $entity->setModified(new DateTime());
        $em->persist($entity);
        $em->flush();
        $this->getRequest()->getSession()->getFlashBag()->add('success', 'Value successfully removed');
        return $this->redirect($this->generateUrl('yoghurt_entity_edit', array('id' => $entityId)));
    }

    /**
     * @Route("/status/{id}/{status}", name="yoghurt_entity_status")
     * , requirements={"status" = "\d"})
     */
    public function statusAction($id, $status) {

        if ($status != Entity\Entity::STATUS_DISABLED && $status != Entity\Entity::STATUS_ENABLED && $status != Entity\Entity::STATUS_TEST) {
            throw new Exception('Unallowed status!');
        }

        $req = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SpoiledMilkYoghurtBundle:Entity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Entity.');
        }

        $entity->setStatus($status);
        $em->persist($entity);
        $em->flush();
        $req->getSession()->getFlashBag()->add('success', 'Status successfully changed');

        if ($req->get('back')) {
            return $this->redirect($req->get('back'));
        } else {
            return $this->redirect($this->generateUrl('yoghurt_entitytype_show', array('id' => $entity->getEntityType()->getId())));
        }
    }

    /**
     * This method is called via AJAX when the user reorders Entities using
     * drag'n'drop method. It receives the following data in the POST request:<br/>
     * startOrder - (comma delimited string) Array of entity IDs, in the original order<br/>
     * newOrder   - (comma delimited string) Array of entity IDs, now reordered by the user<br/>
     *
     * The method returns a JSON array containing the IDs of
     * Entities, in the new order
     *
     * @Route("/reorder", name="yoghurt_entity_reorder")
     * @Method("post")
     */
    public function reorderAction(Request $request) {
        $startOrder = explode(',', $request->request->get('startOrder'));
        $newOrder = explode(',', $request->request->get('newOrder'));

        if (count($startOrder) != count($newOrder))
            throw new Exception('Start and New order arrays must have the same number of elements');

        $newPositions = $this->getDoctrine()
                ->getRepository('SpoiledMilkYoghurtBundle:Entity')
                ->reorder($startOrder, $newOrder);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent('{"newOrder":' . json_encode($newPositions) . '}');
        return $response;
    }

    // Here be utility functions!

    /**
     * Adds FieldValues to the Entity, based od EntityType
     * @param \SpoiledMilk\YoghurtBundle\Entity\Entity $entity
     */
    private function addFieldValues($entity) {
      
        $yoghurtService = $this->get('yoghurt_service');

        foreach ($entity->getEntityType()->getFields() as $field) {
            $exists = false;
            $fieldValue = null;

            foreach ($entity->getFieldValues() as $fieldVal) {
                if ($fieldVal->getField()->getName() == $field->getName() &&
                        $fieldVal->getField()->getFieldType() == $field->getFieldType()) {

                    $exists = true;
                    break;
                }
            }

            if ($exists) {
                continue;
            }

            $className = 'SpoiledMilk\YoghurtBundle\Entity\\' . $field->getFieldType()->getClassName();
            $fieldValue = new $className;

            if ($fieldValue) {
                $fieldValue->setEntity($entity);
                $fieldValue->setField($field);
                $fieldValue->setPosition(1000 * $field->getPosition() + $entity->countFieldValues() + 1);

                if ($fieldValue instanceof Entity\FileValue)
                    $yoghurtService->checkPrefix($fieldValue);

                $entity->addFieldValue($fieldValue);
            }
        }
    }


    /**
     * Returns a matrix of the following structure:
     *  [field_id, field_label, form]
     *
     * @param \SpoiledMilk\YoghurtBundle\Entity\Entity $entity
     * @return array
     */
    private function getRepeatingForms($entity) {
        $ret = array();

        foreach ($entity->getEntityType()->getFields() as $field) {
            if (!$field->getRepeating() || $field->getFieldType()->getClassName() == 'MapValue')
                continue;

            $className = 'SpoiledMilk\YoghurtBundle\Entity\\' . $field->getFieldType()->getClassName();
            $fieldValue = new $className;
            $fieldValue->setEntity($entity);
            $fieldValue->setField($field);
            $ret[] = array(
                'field_id' => $field->getId(),
                'field_label' => $field->getLabel(),
                'form' => $this
                        ->createForm(new Form\FieldValueType(), $fieldValue, array('attr' => array('repeated' => true)))
                        ->createView()
            );
        }

        return $ret;
    }

    /**
     * Returns an array of names of files belonging to this Entity
     * @param \SpoiledMilk\YoghurtBundle\Entity\Entity $entity
     */
    private function getFiles($entity) {
        $ret = array();

        foreach ($entity->getFieldValues() as $value) {
            if ($value instanceof \SpoiledMilk\YoghurtBundle\Entity\FileValue) {
                $ret[$value->getId()] = $value->getValue();
            }
        }

        return $ret;
    }

    /**
     * Uploads files to server and sets fields accordingly
     *
     * @param array $existingFiles
     * @param \SpoiledMilk\YoghurtBundle\Entity\Entity $entity
     */
    private function uploadFiles($existingFiles, $entity) {

        foreach ($entity->getFieldValues() as $value) {
            if ($value instanceof \SpoiledMilk\YoghurtBundle\Entity\FileValue) {

                if ($value->getValue() != null) {
                    $yoghurtService = $this->get('yoghurt_service');

                    if (isset($existingFiles[$value->getId()])) {
                        $yoghurtService->deleteFile($existingFiles[$value->getId()]);
                    }

                    // upload file and set file name in base
                    $fileName = $yoghurtService->uploadFile($value->getValue(), $value->getPrefix() . uniqid());
                    $value->setValue($fileName);
                } else if (isset($existingFiles[$value->getId()])) {
                    // preserve existing
                    $value->setValue($existingFiles[$value->getId()]);
                }
            }
        }
    }

    /**
     * Deletes all files on server that are connected to the given Entity instance
     *
     * @param \SpoiledMilk\YoghurtBundle\Entity\Entity $entity
     */
    private function deleteFiles(Entity\Entity $entity) {
        foreach ($entity->getFieldValues() as $value) {
            if ($value instanceof \SpoiledMilk\YoghurtBundle\Entity\FileValue) {

                if ($value->getValue() != null) {
                    $this->get('yoghurt_service')->deleteFile($value->getPrefix() . $value->getValue());
                }
            }
        }
    }

    private function setTerms(Entity\Entity $entity, $termIds = '') {
        if (!$termIds)
            return;

        $termIds = explode(',', $termIds);
        $terms = array();
        $termRepo = $this->getDoctrine()->getRepository('SpoiledMilkYoghurtBundle:Term');

        foreach ($termIds as $termId) {
            $terms[] = $termRepo->find($termId);
        }

        $entity->setTerms($terms);
    }

}
