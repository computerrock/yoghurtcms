<?php

namespace SpoiledMilk\YoghurtBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SpoiledMilk\YoghurtBundle\Entity\EntityType;
use SpoiledMilk\YoghurtBundle\Entity\Entity;
use SpoiledMilk\YoghurtBundle\Form\EntityTypeType;
use SpoiledMilk\YoghurtBundle\Form\FieldType;
use SpoiledMilk\YoghurtBundle\Entity\Field;
use SpoiledMilk\YoghurtBundle\Entity\EntityTypeVocabulary;
use SpoiledMilk\YoghurtBundle\Form\EntityTypeVocabularyType;

/**
 * EntityType controller.
 *
 * @Route("/entitytype")
 */
class EntityTypeController extends DefaultController {

    /**
     * Lists all EntityType entities.
     *
     * @Route("/", name="yoghurt_entitytype")
     * @Template()
     */
    public function indexAction() {
        $types = $this->getEntityTypes();
        $formNew = $this->createForm(new EntityTypeType(), new EntityType());

        return array(
            'types' => $types,
            'formNew' => $formNew->createView());
    }

    /**
     * Displays a form to create a new EntityType entity.
     *
     * @Route("/new", name="yoghurt_entitytype_new")
     * @Template()
     */
    public function newAction() {
        $entityType = new EntityType();
        $form = $this->createForm(new EntityTypeType(), $entityType);

        return array(
            'entityType' => $entityType,
            'form' => $form->createView()
        );
    }

    /**
     * Creates a new EntityType entity.
     *
     * @Route("/create", name="yoghurt_entitytype_create")
     * @Method("post")
     * @Template("SpoiledMilkYoghurtBundle:EntityType:new.html.twig")
     */
    public function createAction() {
        $entityType = new EntityType();
        $request = $this->getRequest();
        $form = $this->createForm(new EntityTypeType(), $entityType);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entityType);
            $em->flush();

            return $this->redirect($this->generateUrl('yoghurt_entitytype_edit', array('id' => $entityType->getId())));
        }

        return array(
            'entityType' => $entityType,
            'form' => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing EntityType entity.
     *
     * @Route("/edit/{id}", name="yoghurt_entitytype_edit")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entityType = $em->getRepository('SpoiledMilkYoghurtBundle:EntityType')->find($id);

        if (!$entityType) {
            throw $this->createNotFoundException('Unable to find EntityType entity.');
        }

        $editForm = $this->createForm(new EntityTypeType(), $entityType);        
        $fieldForm = $this->createForm(new FieldType(), new Field());
        $vocabularyForm = $this->createForm(
                new EntityTypeVocabularyType(), new EntityTypeVocabulary($entityType));

        return array(
            'entityType' => $entityType,
            'edit_form' => $editForm->createView(),
            'field_form' => $fieldForm->createView(),
            'vocabulary_form' => $vocabularyForm->createView(),
        );
    }

    /**
     * Edits an existing EntityType entity.
     *
     * @Route("/update/{id}", name="yoghurt_entitytype_update")
     * @Method("post")
     * @Template("SpoiledMilkYoghurtBundle:EntityType:edit.html.twig")
     */
    public function updateAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entityType = $em->getRepository('SpoiledMilkYoghurtBundle:EntityType')->find($id);

        if (!$entityType) {
            throw $this->createNotFoundException('Unable to find EntityType entity.');
        }

        $editForm = $this->createForm(new EntityTypeType(), $entityType);
        $editForm->bindRequest($this->getRequest());

        if ($editForm->isValid()) {
            $em->persist($entityType);
            $em->flush();
            $this->getRequest()->getSession()->getFlashBag()->add('success', 'Entity type successfully updated.');
            return $this->redirect($this->generateUrl('yoghurt_entitytype_edit', array('id' => $id)));
        } else {
            $fieldForm = $this->createForm(new FieldType(), new Field());
            $vocabularyForm = $this->createForm(new EntityTypeVocabularyType());
            $this->getRequest()->getSession()->getFlashBag()->add('error', 'The form contains errors.');

            $errors = $this->getFormErrors($editForm);
            for ($i = 0; $i < sizeof($errors); $i++) {
                $this->getRequest()->getSession()->getFlashBag()->add("error $i", $errors[$i]);
            }

            return array(
                'entityType' => $entityType,
                'edit_form' => $editForm->createView(),
                'field_form' => $fieldForm->createView(),
                'vocabulary_form' => $vocabularyForm->createView(),
            );
        }
    }

    /**
     * Deletes the EntityType with given ID.
     * @Route("/delete/{id}", name="yoghurt_entitytype_delete")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SpoiledMilkYoghurtBundle:EntityType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EntityType entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('yoghurt_entitytype'));
    }

    /**
     * 
     * @Route("/order/{direction}/{id}", name="yoghurt_entitytype_order")
     */
    public function orderEntityTypeAction($id, $direction) {
        $types = $this->getEntityTypes();
        $changed = $this->swapPositions($types, $id, $direction);

        if ($changed) {
            $em = $this->getDoctrine()->getManager();

            foreach ($changed as $value) {
                $em->persist($value);
            }

            $em->flush();
        }

        return $this->redirect($this->generateUrl('yoghurt_entitytype'));
    }
    
    /**
     * @Route("/reorder", name="yoghurt_entitytype_reorder")
     * @Method("post")
     */
    public function ajaxReorderAction() {
        $oldOrder = $this->getRequest()->get('oldOrder');
        $oldOrder = explode(',', $oldOrder);
        
        $newOrder = $this->getRequest()->get('newOrder');
        $newOrder = explode(',', $newOrder);
        
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SpoiledMilkYoghurtBundle:EntityType');
        
        for ($i = 0; $i < count($oldOrder); $i++) {
            $entityType = $repo->find($oldOrder[$i]);
            $newPos = array_search($oldOrder[$i], $newOrder);
            $delta = $newPos - $i;
            
            if ($delta != 0) {
                $entityType->setPosition($entityType->getPosition() + $delta);
                $em->persist($entityType);
            }
        }
        
        $em->flush();
        $ret = new \Symfony\Component\HttpFoundation\Response();
        $ret->headers->set('Content-Type', 'text/plain');
        $ret->setContent('ok');
        return $ret;
    }

    /**
     * 
     * @Route("/field/add/{id}", name="yoghurt_entitytype_addfield")
     * @Method("post")
     */
    public function addFieldAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entityType = $em->getRepository('SpoiledMilkYoghurtBundle:EntityType')->find($id);

        if (!$entityType) {
            throw $this->createNotFoundException('EntityType not found!');
        }

        $field = new Field();
        $field->setEntityType($entityType);
        $form = $this->createForm(new FieldType(), $field);
        $form->bindRequest($this->getRequest());

        if ($form->isValid()) {
            $em->persist($field);
            $this->addFieldToEntities($field, $entityType);
            $em->flush();
            $this->getRequest()->getSession()->getFlashBag()->add('success', 'Field successfully added.');
        } else {
            $this->getRequest()->getSession()->getFlashBag()->add('error', 'The form contained errors. Field was not added.');
        }

        return $this->redirect($this->generateUrl('yoghurt_entitytype_edit', array('id' => $id)));
    }

    /**
     * @Route("/field/delete/{entity_type_id}/{field_id}", name="yoghurt_entitytype_deletefield")
     */
    public function deleteFieldAction($entity_type_id, $field_id) {
        $em = $this->getDoctrine()->getManager();
        $entityType = $em->getRepository('SpoiledMilkYoghurtBundle:EntityType')->find($entity_type_id);

        if (!$entityType) {
            throw $this->createNotFoundException('EntityType not found!');
        }

        $field = $em->getRepository('SpoiledMilkYoghurtBundle:Field')->find($field_id);

        if (!$field) {
            throw $this->createNotFoundException('Field not found!');
        }

        $em->remove($field);
        $em->flush();
        $this->getRequest()->getSession()->getFlashBag()->add('success', 'Field successfully deleted.');

        return $this->redirect($this->generateUrl('yoghurt_entitytype_edit', array('id' => $entity_type_id)));
    }

    /**
     * @Route("/vocabulary/add/{id}", name="yoghurt_entitytype_addvocabulary")
     * @Method("post")
     */
    public function addVocabularyAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entityType = $em->getRepository('SpoiledMilkYoghurtBundle:EntityType')->find($id);

        if (!$entityType) {
            throw $this->createNotFoundException('EntityType not found!');
        }

        $etv = new EntityTypeVocabulary();
        $vocabularyForm = $this->createForm(new EntityTypeVocabularyType(), $etv);
        $vocabularyForm->bindRequest($this->getRequest());


        if ($vocabularyForm->isValid()) {
            $em->persist($etv);
            $em->flush();
            $this->getRequest()->getSession()->getFlashBag()->add('success', 'Vocabulary successfully added.');
        } else {
            $this->getRequest()->getSession()->getFlashBag()->add('error', 'The form contained errors. Vocabulary was not added.');
            $errors = $this->getFormErrors($vocabularyForm);

            for ($i = 0; $i < sizeof($errors); $i++) {
                $this->getRequest()->getSession()->getFlashBag()->add("error $i", $errors[$i]);
            }
        }

        return $this->redirect($this->generateUrl('yoghurt_entitytype_edit', array('id' => $id)));
    }

    /**
     * @Route("/vocabulary/delete/{entityTypeId}/{etvId}", name="yoghurt_entitytype_deletevocabulary") 
     */
    public function deleteVocabularyAction($entityTypeId, $etvId) {
        $em = $this->getDoctrine()->getManager();

        $entityType = $em->getRepository('SpoiledMilkYoghurtBundle:EntityType')->find($entityTypeId);

        if (!$entityType) {
            throw $this->createNotFoundException('EntityType not found!');
        }

        $etv = $em->getRepository('SpoiledMilkYoghurtBundle:EntityTypeVocabulary')->find($etvId);

        if (!$etv) {
            throw $this->createNotFoundException('Vocabulary not found!');
        }

        // Remove all the selected Vocabulary's terms from Entities of the selected Type
        $entities = $em->getRepository('SpoiledMilkYoghurtBundle:Entity')->fetchByType($entityType);
        foreach ($entities as $entity) {
            foreach ($entity->getTerms() as $term) {
                if ($term->getVocabulary()->getId() == $etv->getVocabulary()->getId()) {
                    $entity->removeTerm($term);
                    $em->persist($entity);
                }
            }
        }

        $em->remove($etv);
        $em->flush();

        return $this->redirect($this->generateUrl('yoghurt_entitytype_edit', array(
                            'id' => $entityTypeId
                        )));
    }

    /**
     * Lists all entities for given Entity Type slug.
     *
     * @Route(
     *   "/show/{id}/{page}",
     *   name="yoghurt_entitytype_show",
     *   defaults={"page"=1})
     * @Template()
     */
    public function showAction($id, $page) {
        $em = $this->getDoctrine()->getManager();

        $entityType = $em->getRepository('SpoiledMilkYoghurtBundle:EntityType')
                ->find($id);

        if (!$entityType) {
            throw $this->createNotFoundException('Entity Type not found!');
        }

        $req = $this->getRequest();
        $limit = $req->get('limit', 10);
        
        if (!$limit) {
            $limit = 10;
        }
        
        $operation = $req->get('operation');

        if ($operation) {
            $params = $req->query->all();
            $selectedIds = array();

            foreach ($params as $key => $val) {
                // Get selected Entities
                if (stripos($key, 'cb-') !== false)
                    $selectedIds[] = $val;
            }
            
            $entities = null;
            
            if ($selectedIds) 
                $entities = $em->getRepository('SpoiledMilkYoghurtBundle:Entity')->fetchMultipleByIds($selectedIds);
            
            if ($entities) {
                if ($operation == 'delete') {

                    foreach ($entities as $entity) {
                        $em->remove($entity);
                    }

                } else if ($operation == 's0') {
                    
                    foreach ($entities as $entity) {
                        $entity->setStatus(Entity::STATUS_DISABLED);
                        $em->persist($entity);
                    }
                    
                } else if ($operation == 's1') {
                    
                    foreach ($entities as $entity) {
                        $entity->setStatus(Entity::STATUS_ENABLED);
                        $em->persist($entity);
                    }
                    
                } else if ($operation == 's2') {
                    
                    foreach ($entities as $entity) {
                        $entity->setStatus(Entity::STATUS_TEST);
                        $em->persist($entity);
                    }
                    
                }
                
                $em->flush();
            }
            
            return $this->redirect($this->generateUrl('yoghurt_entitytype_show', array('id' => $id, 'page' => $page)) . '?limit=' . $limit);
        }

        $query = $em
                ->getRepository('SpoiledMilkYoghurtBundle:Entity')
                ->getQueryBuilderForPaginated($entityType->getId())
                ->getQuery();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($query, $page, $limit);

        $ret = compact('pagination');
        $ret['entityType'] = $entityType;
        $ret['limit'] = $limit;

        return $ret;
    }
    
    /**
     * Adds the given field to all entities of the given entity type. Note that 
     * this method does not flush changes to database
     * 
     * @param \SpoiledMilk\YoghurtBundle\Entity\Field $field
     * @param \SpoiledMilk\YoghurtBundle\Entity\EntityType $entityType
     */
    private function addFieldToEntities(Field $field, EntityType $entityType) {
        $em = $this->getDoctrine()->getManager();
        
        foreach ($entityType->getEntities() as $entity) {
            $className = 'SpoiledMilk\YoghurtBundle\Entity\\' . $field->getFieldType()->getClassName();
            $fieldValue = new $className;

            $fieldValue->setEntity($entity);
            $fieldValue->setField($field);
            $fieldValue->setPosition(1000 * $field->getPosition() + $entity->countFieldValues() + 1);

            if ($fieldValue instanceof Entity\FileValue)
                $this->checkPrefix($fieldValue);

            $entity->addFieldValue($fieldValue);
            $em->persist($fieldValue);
        }
    }

}
