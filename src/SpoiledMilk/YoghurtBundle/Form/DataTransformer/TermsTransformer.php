<?php

namespace SpoiledMilk\YoghurtBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use SpoiledMilk\YoghurtBundle\Entity\Term;

class TermsTransformer implements DataTransformerInterface {

    private $entityManager;

    function __construct($entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms a string into an object
     * @param string $value 
     */
    public function reverseTransform($value) {
        $value = preg_replace('/,\s*$/', '', $value);
        
        $ret = new \Doctrine\Common\Collections\ArrayCollection();
        if (!$value) {
            return $ret;
        }

        $value = explode(',', $value);
        $repo = $this->entityManager->getRepository('SpoiledMilkYoghurtBundle:Term');

        foreach ($value as $strTerm) {
            $term = $repo->findOneBy(array(
                'term' => trim($strTerm)
            ));
            
            if(!$term) {
                throw new TransformationFailedException('Term "' . $strTerm . '" doesn\'t exist!');
            }
            
            $ret[] = $term;
        }

        return $ret;
    }

    /**
     * Transforms and object to a string
     * @var \Doctrine\Common\Collections\ArrayCollection $value
     */
    public function transform($value) {
        $ret = '';

        if ($value == null) {
            return $ret;
        }

        foreach ($value as $term) {
            if ($ret)
                $ret .= ', ';
            $ret .= $term->getTerm();
        }

        return $ret;
    }

}