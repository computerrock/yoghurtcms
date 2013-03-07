<?php

namespace SpoiledMilk\YoghurtBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SpoiledMilk\YoghurtBundle\Entity\Vocabulary;
use SpoiledMilk\YoghurtBundle\Form\VocabularyType;
use SpoiledMilk\YoghurtBundle\Form\TermType;
use SpoiledMilk\YoghurtBundle\Entity\Term;
use SpoiledMilk\YoghurtBundle\Form\EntityTypeVocabularyType;
use SpoiledMilk\YoghurtBundle\Entity\EntityTypeVocabulary;

/**
 * Vocabulary controller.
 *
 * @Route("/vocabulary")
 */
class VocabularyController extends DefaultController {

    /**
     * Lists all Vocabulary entities.
     *
     * @Route("/", name="yoghurt_vocabulary")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('SpoiledMilkYoghurtBundle:Vocabulary')->findAll();
        $formNew = $this->createForm(new VocabularyType(), new Vocabulary());

        return array(
            'entities' => $entities,
            'formNew' => $formNew->createView()
        );
    }

    /**
     * Displays a form to create a new Vocabulary entity.
     *
     * @Route("/new", name="yoghurt_vocabulary_new")
     * @Template()
     */
    public function newAction() {
        $entity = new Vocabulary();
        $form = $this->createForm(new VocabularyType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );
    }

    /**
     * Creates a new Vocabulary entity.
     *
     * @Route("/create", name="yoghurt_vocabulary_create")
     * @Method("post")
     * @Template("SpoiledMilkYoghurtBundle:Vocabulary:new.html.twig")
     */
    public function createAction() {
        $entity = new Vocabulary();
        $request = $this->getRequest();
        $form = $this->createForm(new VocabularyType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $request->getSession()->setFlash('success', 'Vocabulary successfully created.');
            return $this->redirect($this->generateUrl('yoghurt_vocabulary_edit', array('id' => $entity->getId())));
        } else {
            $request->getSession()->setFlash('error', 'The form contains errors. Vocabulary was not created.');
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Vocabulary entity.
     *
     * @Route("/{id}/edit", name="yoghurt_vocabulary_edit")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SpoiledMilkYoghurtBundle:Vocabulary')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vocabulary entity.');
        }

        $termForm = $this->createForm(new TermType(), new Term(), array('vocabularyId' => $id));
        $editForm = $this->createForm(new VocabularyType(), $entity);
        $deleteForm = $this->createDeleteForm($id);
        $etvForm = $this->createForm(
                new EntityTypeVocabularyType(), 
                new EntityTypeVocabulary(null, $entity));
        
        $terms = array();
        foreach ($entity->getTerms() as $term) {
            if(!$term->getParent()) {
                $this->addTermAndChildren($terms, $term);
            }
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'term_form' => $termForm->createView(),
            'terms' => $terms,
            'etv_form' => $etvForm->createView(),
        );
    }

    /**
     * Edits an existing Vocabulary entity.
     *
     * @Route("/{id}/update", name="yoghurt_vocabulary_update")
     * @Method("post")
     * @Template("SpoiledMilkYoghurtBundle:Vocabulary:edit.html.twig")
     */
    public function updateAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SpoiledMilkYoghurtBundle:Vocabulary')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vocabulary entity.');
        }

        $editForm = $this->createForm(new VocabularyType(), $entity);
        $request = $this->getRequest();
        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            $request->getSession()->setFlash('success', 'Changes successfully saved.');
            return $this->redirect($this->generateUrl('yoghurt_vocabulary_edit', array('id' => $id)));
        }
        
        $deleteForm = $this->createDeleteForm($id);
        $termForm = $this->createForm(new TermType(), new Term(), array('vocabularyId' => $id));
        $etvForm = $this->createForm(
                new EntityTypeVocabularyType(), 
                new EntityTypeVocabulary(null, $entity));
        
        $terms = array();
        foreach ($entity->getTerms() as $term) {
            if(!$term->getParent()) {
                $this->addTermAndChildren($terms, $term);
            }
        }
        
        $request->getSession()->setFlash('error', 'The form contains errors.');
        $errors = $this->getFormErrors($editForm);
        for($i = 0; $i < sizeof($errors); $i++) {
            $request->getSession()->setFlash("error $i", $errors[$i]);
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'term_form' => $termForm->createView(),
            'terms' => $terms,
            'etv_form' => $etvForm->createView(),
        );
    }

    /**
     * Deletes a Vocabulary entity.
     *
     * @Route("/{id}/delete", name="yoghurt_vocabulary_delete")
     * @Method("post")
     */
    public function deleteAction($id) {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SpoiledMilkYoghurtBundle:Vocabulary')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Vocabulary entity.');
            }

            $em->remove($entity);
            $em->flush();
            $request->getSession()->setFlash('success', 'Vocabulary successfully deleted.');
        }

        return $this->redirect($this->generateUrl('yoghurt_vocabulary'));
    }

    /**
     * @Route("/{id}/addterm", name="yoghurt_vocabulary_addterm")
     * @Method("post")
     */
    public function addTermAction($id) {
        $em = $this->getDoctrine()->getManager();
        $vocabulary = $em->getRepository('SpoiledMilkYoghurtBundle:Vocabulary')->find($id);

        if (!$vocabulary) {
            throw $this->createNotFoundException('Unable to find Vocabulary entity.');
        }

        $term = new Term();
        $termForm = $this->createForm(new TermType(), $term, array('vocabularyId' => $id));
        $termForm->bindRequest($this->getRequest());

        if($termForm->isValid()) {
            $term->setVocabulary($vocabulary);
            $em->persist($term);
            $em->flush();
            $this->getRequest()->getSession()->setFlash('success', 'Term successfully added.');
        } else {
            $this->getRequest()->getSession()->setFlash('error', 'The form contained errors. Term was not added.');
        }

        return $this->redirect($this->generateUrl('yoghurt_vocabulary_edit', array('id' => $id)));
    }

    /**
     * @Route("/{vocabularyId}/removeterm/{termId}", name="yoghurt_vocabulary_removeterm")
     */
    public function removeTermAction($vocabularyId, $termId) {
        $em = $this->getDoctrine()->getManager();
        $vocabulary = $em->getRepository('SpoiledMilkYoghurtBundle:Vocabulary')->find($vocabularyId);

        if (!$vocabulary) {
            throw $this->createNotFoundException('Unable to find Vocabulary entity.');
        }

        $term = $em->getRepository('SpoiledMilkYoghurtBundle:Term')->find($termId);

        if (!$term) {
            throw $this->createNotFoundException('Unable to find Term.');
        }

        $em->remove($term);
        $em->flush();
        $this->getRequest()->getSession()->setFlash('success', 'Term successfully deleted.');
        return $this->redirect($this->generateUrl('yoghurt_vocabulary_edit', array('id' => $vocabularyId)));
    }
    
    /**
     * @Route("/{id}/vocabulary/add", name="yoghurt_vocabulary_addEntityType")
     * @Method("post")
     */
    public function addEntityTypeAction($id) {
        $em = $this->getDoctrine()->getManager();
        $vocabulary = $em->getRepository('SpoiledMilkYoghurtBundle:Vocabulary')->find($id);

        if (!$vocabulary) {
            throw $this->createNotFoundException('Vocabulary not found!');
        }
        
        $etv = new EntityTypeVocabulary();
        $etvForm = $this->createForm(new EntityTypeVocabularyType(), $etv);
        $etvForm->bindRequest($this->getRequest());
        
        
        if($etvForm->isValid()) {
            $em->persist($etv);
            $em->flush();
            $this->getRequest()->getSession()->setFlash('success', 'Entity type successfully added.');
        } else {
            $this->getRequest()->getSession()->setFlash('error', 'The form contained errors. Entity type was not added.');
            $errors = $this->getFormErrors($etvForm);
            
            for($i = 0; $i < sizeof($errors); $i++) {
                $this->getRequest()->getSession()->setFlash("error $i", $errors[$i]);
            }
        }
        
        return $this->redirect($this->generateUrl('yoghurt_vocabulary_edit', array('id' => $id)));
    }
    
    /**
     * @Route("/{vocabularyId}/entityType/remove/{etvId}", name="yoghurt_vocabulary_removeEntityType") 
     */
    public function removeEntityTypeAction($vocabularyId, $etvId) {
        $em = $this->getDoctrine()->getManager();
        
        $vocabulary = $em->getRepository('SpoiledMilkYoghurtBundle:Vocabulary')->find($vocabularyId);

        if (!$vocabulary) {
            throw $this->createNotFoundException('Vocabulary not found!');
        }
        
        $etv = $em->getRepository('SpoiledMilkYoghurtBundle:EntityTypeVocabulary')->find($etvId);
        
        if (!$etv) {
            throw $this->createNotFoundException('Entity type not found!');
        }
        
        // Remove all the selected Vocabulary's terms from Entities of the selected Type
        $entities = $em->getRepository('SpoiledMilkYoghurtBundle:Entity')
                ->fetchByType($etv->getEntityType());
        
        foreach ($entities as $entity) {
            foreach ($entity->getTerms() as $term) {
                if($term->getVocabulary()->getId() == $vocabularyId) {
                    $entity->removeTerm($term);
                    $em->persist($entity);
                }
            }
        }
        
        $em->remove($etv);
        $em->flush();
        
        return $this->redirect($this->generateUrl('yoghurt_vocabulary_edit', array(
            'id' => $vocabularyId
        )));
    }

    /**
     * @Route("/{vocabularyId}/edit-term/{termId}", name="yoghurt_vocabulary_editTerm")
     *
     * @Template()
     */
    public function editTermAction($vocabularyId, $termId) {
        $em = $this->getDoctrine()->getManager();

        $term = $em->getRepository('SpoiledMilkYoghurtBundle:Term')->find($termId);

        if (!$term) {
            throw $this->createNotFoundException('Unable to find Term.');
        }

        $editForm = $this->createForm(new TermType(), $term);

        return array(
            'term' => $term,
            'edit_form' => $editForm->createView()
        );
    }

    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

    /**
     *
     * @param array $array
     * @param Term $term
     */
    private function addTermAndChildren(&$array, $term) {
        $array[] = $term;

        foreach ($term->getChildren() as $child) {
            $this->addTermAndChildren($array, $child);
        }
    }

}
