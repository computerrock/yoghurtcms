<?php

namespace SpoiledMilk\YoghurtBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class FieldType {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    private $name;
    
    /**
     *
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $className;
    
    /**
     * @ORM\OneToMany(targetEntity="FieldTypeOption", mappedBy="fieldType", cascade={"all"})
     */
    private $options;

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
    
    public function getClassName() {
        return $this->className;
    }

    public function setClassName($className) {
        $this->className = $className;
    }
    
    public function getOptions() {
        return $this->options;
    }

    public function setOptions($options) {
        $this->options = $options;
    }

    public function __toString() {
        return $this->name;
    }

}