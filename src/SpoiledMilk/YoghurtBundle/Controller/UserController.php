<?php

namespace SpoiledMilk\YoghurtBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SpoiledMilk\YoghurtBundle\Entity\User;
use SpoiledMilk\YoghurtBundle\Form\UserType;

/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends Controller {

    /**
     * Lists all User entities.
     *
     * @Route("/", name="yoghurt_user")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('SpoiledMilkYoghurtBundle:User')->findAll();
        $userForm = $this->createForm(new UserType());

        return array(
            'entities' => $entities,
            'userForm' => $userForm->createView()
        );
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="yoghurt_user_new")
     * @Template()
     */
    public function newAction() {
        $entity = new User();
        $form = $this->createForm(new UserType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/create", name="yoghurt_user_create")
     * @Method("post")
     * @Template("SpoiledMilkYoghurtBundle:User:new.html.twig")
     */
    public function createAction() {
        $entity = new User();
        $request = $this->getRequest();
        $form = $this->createForm(new UserType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $encoder = $this->container
                    ->get('security.encoder_factory')
                    ->getEncoder($entity);
            $entity->setPassword($encoder->encodePassword($entity->getPassword(), $entity->getSalt()));

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $request->getSession()->setFlash('success', 'User successfully created.');
            return $this->redirect($this->generateUrl('yoghurt_user_edit', array('id' => $entity->getId())));
        }

        $request->getSession()->setFlash('error', 'The form containes errors. User was not created.');
        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="yoghurt_user_edit")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SpoiledMilkYoghurtBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createForm(new UserType(), $entity);
        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}/update", name="yoghurt_user_update")
     * @Method("post")
     * @Template("SpoiledMilkYoghurtBundle:User:edit.html.twig")
     */
    public function updateAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SpoiledMilkYoghurtBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $oldPass = $entity->getPassword();
        $editForm = $this->createForm(new UserType(), $entity);
        $editForm->remove('password');
        $request = $this->getRequest();
        $editForm->bindRequest($request);
        $entity->setPassword($oldPass);

        if ($editForm->isValid()) {
            if ($entity->getNewPassword()) {
                $encoder = $this->container
                        ->get('security.encoder_factory')
                        ->getEncoder($entity);
                $entity->setSalt();
                $entity->setPassword($encoder->encodePassword($entity->getNewPassword(), $entity->getSalt()));
            }

            $em->persist($entity);
            $em->flush();

            $request->getSession()->setFlash('success', 'Changes successfully saved.');
            return $this->redirect($this->generateUrl('yoghurt_user_edit', array('id' => $id)));
        }

        $request->getSession()->setFlash('error', 'The form containes errors. Changes were not saved.');
        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a User entity.
     *
     * @Route("/{id}/delete", name="yoghurt_user_delete")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SpoiledMilkYoghurtBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $em->remove($entity);
        $em->flush();
        $this->getRequest()->getSession()->setFlash('success', 'User successfully deleted.');
        return $this->redirect($this->generateUrl('yoghurt_user'));
    }

}
