<?php

namespace SpoiledMilk\YoghurtBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SpoiledMilk\YoghurtBundle\Entity\Field;
use SpoiledMilk\YoghurtBundle\Form\FieldType;

/**
 * Field controller.
 *
 * @Route("/field")
 */
class FieldController extends Controller
{
    /**
     * Lists all Field entities.
     *
     * @Route("/", name="field")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SpoiledMilkYoghurtBundle:Field')->findAll();

        return array('entities' => $entities);
    }

    /**
     * Finds and displays a Field entity.
     *
     * @Route("/{id}/show", name="field_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SpoiledMilkYoghurtBundle:Field')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Field entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        );
    }

    /**
     * Displays a form to create a new Field entity.
     *
     * @Route("/new", name="field_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Field();
        $form   = $this->createForm(new FieldType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new Field entity.
     *
     * @Route("/create", name="field_create")
     * @Method("post")
     * @Template("SpoiledMilkYoghurtBundle:Field:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new Field();
        $request = $this->getRequest();
        $form    = $this->createForm(new FieldType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('field_show', array('id' => $entity->getId())));
            
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Field entity.
     *
     * @Route("/{id}/edit", name="field_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SpoiledMilkYoghurtBundle:Field')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Field entity.');
        }

        $editForm = $this->createForm(new FieldType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Field entity.
     *
     * @Route("/{id}/update", name="field_update")
     * @Method("post")
     * @Template("SpoiledMilkYoghurtBundle:Field:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SpoiledMilkYoghurtBundle:Field')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Field entity.');
        }

        $editForm   = $this->createForm(new FieldType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('field_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Field entity.
     *
     * @Route("/{id}/delete", name="field_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SpoiledMilkYoghurtBundle:Field')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Field entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('field'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
