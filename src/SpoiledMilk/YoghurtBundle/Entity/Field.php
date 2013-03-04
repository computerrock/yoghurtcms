<?php

namespace SpoiledMilk\YoghurtBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\HasLifeCycleCallbacks
 */
class Field
{
    /**
     * @var integer $id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var string $name
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $name;
    
    /**
     *
     * @var string $label 
     * @ORM\Column(name="label", type="string", length=255, nullable=false)
     */
    private $label;

    /**
     * @var \SpoiledMilk\YoghurtBundle\Entity\FieldType $fieldType
     * @ORM\ManyToOne(targetEntity="FieldType")
     * @ORM\JoinColumn(name="field_type_id", referencedColumnName="id") 
     */
    private $fieldType;

    /**
     * @var integer $position
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position;
    
    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $repeating;

    /**
     * @var string $description
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;
    
    /**
     * @ORM\OneToMany(targetEntity="FieldMeta", mappedBy="field", cascade={"all"})
     */
    private $fieldMeta;

    /**
     * @var \SpoiledMilk\YoghurtBundle\Entity\EntityType $entityType
     *
     * @ORM\ManyToOne(targetEntity="EntityType", inversedBy="fields")
     * @ORM\JoinColumn(name="entity_type_id", referencedColumnName="id")
     */
    private $entityType;
    
    /**
     * @ORM\OneToMany(targetEntity="FieldValue", mappedBy="field", cascade={"all"})
     */
    private $fieldValues;
    
    public function __construct() {
        $this->repeating = false;
        $this->fieldMeta = new ArrayCollection();
        $this->fieldValues = new ArrayCollection();
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

    public function getLabel() {
        return $this->label;
    }

    public function setLabel($label) {
        $this->label = $label;
    }

    public function getFieldType() {
        return $this->fieldType;
    }

    public function setFieldType($fieldType) {
        $this->fieldType = $fieldType;
    }

    public function getPosition() {
        return $this->position;
    }

    public function setPosition($position) {
        $this->position = $position;
    }
    
    public function getRepeating() {
        return $this->repeating;
    }

    public function setRepeating($repeating) {
        $this->repeating = $repeating;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getEntityType() {
        return $this->entityType;
    }

    public function setEntityType(\SpoiledMilk\YoghurtBundle\Entity\EntityType $entityType) {
        $this->entityType = $entityType;
    }
    
    public function getFieldMeta() {
        return $this->fieldMeta;
    }
    
    public function setFieldMeta($fieldMeta) {
        $this->fieldMeta = $fieldMeta;
    }
    
    public function addFieldMeta(\SpoiledMilk\YoghurtBundle\Entity\FieldMeta $fieldMeta) {
        $this->fieldMeta->add($fieldMeta);
    }

    public function getFieldValues() {
        return $this->fieldValues;
    }

    public function setFieldValues($fieldValues) {
        $this->fieldValues = $fieldValues;
    }

    public function addFieldValue(\SpoiledMilk\YoghurtBundle\Entity\FieldValue $value) {
        $this->fieldValues->add($value);
    }
    
    /**
     * @ORM\PrePersist 
     * @ORM\PreUpdate
     */
    public function prePersist() {
        if(!$this->position) {
            $this->position = $this->entityType->getMaxFieldPosition() + 1;
        }
        
        if(!$this->label) {
            $this->label = ucfirst($this->name);
        }
    }
}