<?php

namespace SpoiledMilk\YoghurtBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SpoiledMilk\YoghurtBundle\Entity\FieldMeta;
use SpoiledMilk\YoghurtBundle\Form\FieldMetaType;

/**
 * FieldMeta controller.
 *
 * @Route("/fieldmeta")
 */
class FieldMetaController extends Controller {

    /**
     * Lists all FieldMeta entities.
     *
     * @Route("/{field_id}", name="yoghurt_fieldmeta")
     * @Template()
     */
    public function indexAction($field_id) {
        $em = $this->getDoctrine()->getManager();
        $field = $em->getRepository('SpoiledMilkYoghurtBundle:Field')->find($field_id);
        
        if(!$field) {
            throw $this->createNotFoundException('Field entity not found!');
        }
        
        $deleteForms = array();
        $editForms = array();
        $formNew = $this->createForm(new FieldMetaType());
        $fieldMeta = $field->getFieldMeta();
        
        foreach ($fieldMeta as $fm) {
            $deleteForms[] = $this->createDeleteForm($fm->getId())->createView();
            $editForms[] = $this->createForm(new FieldMetaType(), $fm)->createView();
        }
        
        return array(
            'field' => $field,
            'fieldMeta' => $fieldMeta,
            'formNew' => $formNew->createView(),
            'editForms' => $editForms,
            'deleteForms' => $deleteForms,
        );
    }

    /**
     * Displays a form to create a new FieldMeta entity.
     *
     * @Route("/new/{field_id}", name="yoghurt_fieldmeta_new")
     * @Template()
     */
    public function newAction($field_id) {
        $em = $this->getDoctrine()->getManager();
        $field = $em->getRepository('SpoiledMilkYoghurtBundle:Field')->find($field_id);
        
        if(!$field) {
            throw $this->createNotFoundException('Field entity not found!');
        }
        
        $entity = new FieldMeta();
        $entity->setField($field);
        $form = $this->createForm(new FieldMetaType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );
    }

    /**
     * Creates a new FieldMeta entity.
     *
     * @Route("/create/{field_id}", name="yoghurt_fieldmeta_create")
     * @Method("post")
     * @Template("SpoiledMilkYoghurtBundle:FieldMeta:new.html.twig")
     */
    public function createAction($field_id) {
        $em = $this->getDoctrine()->getManager();
        $field = $em->getRepository('SpoiledMilkYoghurtBundle:Field')->find($field_id);
        
        if(!$field) {
            throw $this->createNotFoundException('Field entity not found!');
        }
        
        $entity = new FieldMeta();
        $entity->setField($field);
        $form = $this->createForm(new FieldMetaType(), $entity);
        $form->bindRequest($this->getRequest());

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->getRequest()->getSession()->setFlash('success', 'Field setting successfully created.');
        } else {
            $this->getRequest()->getSession()->setFlash('error', 'The form contained errors. Setting was not saved.');            
        }

        return $this->redirect($this->generateUrl('yoghurt_fieldmeta', array('field_id' => $field_id)));
    }

    /**
     * Displays a form to edit an existing FieldMeta entity.
     *
     * @Route("/{id}/edit", name="fieldmeta_edit")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SpoiledMilkYoghurtBundle:FieldMeta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FieldMeta entity.');
        }

        $editForm = $this->createForm(new FieldMetaType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing FieldMeta entity.
     *
     * @Route("/{field_id}/{meta_id}/update", name="yoghurt_fieldmeta_update")
     * @Method("post")
     * @Template("SpoiledMilkYoghurtBundle:FieldMeta:edit.html.twig")
     */
    public function updateAction($field_id, $meta_id) {
        $em = $this->getDoctrine()->getManager();
        $fieldMeta = $em->getRepository('SpoiledMilkYoghurtBundle:FieldMeta')->find($meta_id);

        if (!$fieldMeta) {
            throw $this->createNotFoundException('Unable to find FieldMeta entity.');
        }

        $editForm = $this->createForm(new FieldMetaType(), $fieldMeta);
        $editForm->bindRequest($this->getRequest());

        if ($editForm->isValid()) {
            $em->persist($fieldMeta);
            $em->flush();
            $this->getRequest()->getSession()->setFlash('success', 'Setting successfully updated.');
        } else {
            $this->getRequest()->getSession()->setFlash('error', 'The entered value is illegal, setting was not updated.');
        }
        
        return $this->redirect($this->generateUrl(
                'yoghurt_fieldmeta', 
                array('field_id' => $field_id)));
    }

    /**
     * Deletes a FieldMeta entity.
     *
     * @Route("/{field_id}/{meta_id}/delete", name="yoghurt_fieldmeta_delete")
     * @Method("post")
     */
    public function deleteAction($field_id, $meta_id) {
        $form = $this->createDeleteForm($meta_id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SpoiledMilkYoghurtBundle:FieldMeta')->find($meta_id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find FieldMeta entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('yoghurt_fieldmeta', array('field_id' => $field_id)));
    }

    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

}
