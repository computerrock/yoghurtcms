<?php

namespace SpoiledMilk\YoghurtBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SpoiledMilk\YoghurtBundle\Form\TermType;
use SpoiledMilk\YoghurtBundle\Entity\Term;

/**
 * Term controller.
 *
 * @Route("/term")
 */
class TermController extends DefaultController {

    /**
    * @Route("/{termId}/edit", name="yoghurt_term_edit")
    *
    * @Template()
    */
    public function editAction($termId) {
        $em = $this->getDoctrine()->getManager();

        $term = $em->getRepository('SpoiledMilkYoghurtBundle:Term')->find($termId);

        if (!$term) {
            throw $this->createNotFoundException('Unable to find Term.');
        }

        $editForm = $this->createForm(
                new TermType(), 
                $term, 
                array(
                    'vocabularyId' => $term->getVocabulary()->getId(),
                    'termTreeIds' => $term->getTermTreeIds()
                    )
        );

        return array(
            'term' => $term,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Term entity.
     *
     * @Route("/{id}/update", name="yoghurt_term_update")
     * @Method("post")
     * @Template("SpoiledMilkYoghurtBundle:Term:edit.html.twig")
     */
    public function updateAction($id) {
        $em = $this->getDoctrine()->getManager();

        $term = $em->getRepository('SpoiledMilkYoghurtBundle:Term')->find($id);

        if (!$term) {
            throw $this->createNotFoundException('Unable to find Term entity.');
        }

        $editForm = $this->createForm(
                new TermType(), 
                $term, 
                array(
                    'vocabularyId' => $term->getVocabulary()->getId(),
                    'termTreeIds' => $term->getTermTreeIds()
                    )
        );
        $request = $this->getRequest();
        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($term);
            $em->flush();

            $request->getSession()->setFlash('success', 'Changes successfully saved.');
            return $this->redirect($this->generateUrl('yoghurt_term_edit', array('termId' => $id)));
        }

        $request->getSession()->setFlash('error', 'The form contains errors. Changes were not saved.');
        return array(
            'term' => $term,
            'edit_form' => $editForm->createView(),
        );
    }
}
