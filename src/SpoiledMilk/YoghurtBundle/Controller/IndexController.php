<?php

namespace SpoiledMilk\YoghurtBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("")
 */
class IndexController extends DefaultController {

    /**
     * @Route("/", name="yoghurt_index")
     * @Template()
     */
    public function indexAction() {
        $publishing = $this->getDoctrine()->getRepository('SpoiledMilkYoghurtBundle:Publishing')->find(1);
        
        if (!$publishing) {
            $publishing = new \SpoiledMilk\YoghurtBundle\Entity\Publishing(1);
            $publishing->setLastUpdateDateTime(new \DateTime());
            $this->getDoctrine()->getManager()->persist($publishing);
            $this->getDoctrine()->getManager()->flush();
        }
        
        $lastModified = $this->getDoctrine()
                ->getRepository('SpoiledMilkYoghurtBundle:Entity')
                ->getLastModifiedDateTime();
        return array(
            'publishing' => $publishing,
            'lastModified' => $lastModified
            );
    }
    
    /**
     * @Route("/deleteUnused", name="yoghurt_index_deleteunusedfiles")
     */
    public function deleteUnusedFilesAction() {
        $numberOfRemoved = $this->get('yoghurt_service')->removeUnusedFiles();
        
        if ($numberOfRemoved === 1)
            $msg = "Removed 1 file";
        else
            $msg = "Removed $numberOfRemoved files";
        
        $this->getRequest()->getSession()->getFlashBag()->add('success', $msg);
        return $this->redirect($this->generateUrl('yoghurt_index'));
    }
    
    /**
     * @Route("/publish", name="yoghurt_index_publish")
     */
    public function publishAction() {
        /*
         * TODO implement publishing action here
         * 
         * Example: get all enabled entities and dump them on the drive
         * $enabledEntities = $this->getDoctrine()->getRepository('SpoiledMilkYoghurtBundle:Entity')->fetchAllByStatus(\SpoiledMilk\YoghurtBundle\Entity\Entity::STATUS_ENABLED);
         * $yourService = $this->get('your_service')->dumpEntitiesToDrive($enabledEntities);
         */
        
        // Update last publish datetime
        $publishing = $this->getDoctrine()->getRepository('SpoiledMilkYoghurtBundle:Publishing')->find(1);
        $publishing->setLastPublishDateTime(new \DateTime());
        $this->getDoctrine()->getManager()->persist($publishing);
        $this->getDoctrine()->getManager()->flush();
        
        $this->getRequest()->getSession()->getFlashBag()->add('success', 'Publish successfully compleated');
        return $this->redirect($this->generateUrl('yoghurt_index'));
    }

}