<?php

namespace SpoiledMilk\YoghurtBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity 
 */
class FieldTypeOption {
    
    /**
     *
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     *
     * @var \SpoiledMilk\YoghurtBundle\Entity\FieldType $fieldType
     * @ORM\ManyToOne(targetEntity="FieldType", inversedBy="options")
     * @ORM\JoinColumn(name="field_type_id", referencedColumnName="id")
     */
    private $fieldType;
    
    /**
     *
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $name;
    
    /**
     *
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $value;
    
    /**
     *
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getFieldType() {
        return $this->fieldType;
    }

    public function setFieldType(\SpoiledMilk\YoghurtBundle\Entity\FieldType $fieldType) {
        $this->fieldType = $fieldType;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }


}