<?php

namespace SpoiledMilk\YoghurtBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity()
 */
class Vocabulary {
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank
     */
    private $slug;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Term", mappedBy="vocabulary", cascade={"all"})
     * @ORM\OrderBy({"term"="ASC"})
     */
    private $terms;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="EntityTypeVocabulary", mappedBy="vocabulary", cascade={"all"})
     */
    private $entityTypeVocabularies;
    
    function __construct() {
        $this->terms = new ArrayCollection();
        $this->entityTypeRelationships = new ArrayCollection();
    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getSlug() {
        return $this->slug;
    }

    public function setSlug($slug) {
        $this->slug = $slug;
    }

    public function getTerms() {
        return $this->terms;
    }

    public function setTerms($terms) {
        $this->terms = $terms;
    }

    public function getEntityTypeVocabularies() {
        return $this->entityTypeVocabularies;
    }

    public function setEntityTypeVocabularies($entityTypeVocabularies) {
        $this->entityTypeVocabularies = $entityTypeVocabularies;
    }
    
    public function addEntityTypeVocabulary($entityTypeVocabulary) {
        $this->entityTypeVocabularies[] = $entityTypeVocabulary;
    }


}
