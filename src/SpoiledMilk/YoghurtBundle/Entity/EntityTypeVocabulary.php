<?php

namespace SpoiledMilk\YoghurtBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\HasLifeCycleCallbacks
 * @UniqueEntity(
 *   fields={"entityType", "vocabulary"},
 *   message="You can't add the same Vocabulary twice"
 * )
 */
class EntityTypeVocabulary {
    
    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var \SpoiledMilk\YoghurtBundle\Entity\EntityType
     * @ORM\ManyToOne(targetEntity="EntityType", inversedBy="entityTypeVocabularies")
     * @ORM\JoinColumn(name="entity_type_id", referencedColumnName="id")
     */
    private $entityType;
    
    /**
     * @var \SpoiledMilk\YoghurtBundle\Entity\Vocabulary
     * @ORM\ManyToOne(targetEntity="Vocabulary", inversedBy="entityTypeVocabularies")
     * @ORM\JoinColumn(name="vocabulary_id", referencedColumnName="id")
     */
    private $vocabulary;
    
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $mandatory;
    
    public function __construct($entityType = null, $vocabulary = null) {
        if($entityType)
            $this->entityType = $entityType;
        
        if($vocabulary)
            $this->vocabulary = $vocabulary;
        
        $this->mandatory = false;
    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getEntityType() {
        return $this->entityType;
    }

    public function setEntityType($entityType) {
        $this->entityType = $entityType;
    }
    
    public function getEntityTypeName() {
        if($this->entityType) {
            return $this->entityType->getName();
        } else {
            return '';
        }
    }

    public function getVocabulary() {
        return $this->vocabulary;
    }

    public function setVocabulary($vocabulary) {
        $this->vocabulary = $vocabulary;
    }
    
    public function getVocabularyName() {
        if($this->vocabulary) {
            return $this->vocabulary->getName();
        } else {
            return '';
        }
    }

    public function getMandatory() {
        return $this->mandatory;
    }

    public function setMandatory($mandatory) {
        $this->mandatory = $mandatory;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate 
     */
    public function prePersist() {
        if($this->mandatory === null) {
            $this->mandatory = false;
        }
    }
    
    
}