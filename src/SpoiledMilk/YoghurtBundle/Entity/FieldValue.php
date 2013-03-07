<?php

namespace SpoiledMilk\YoghurtBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;

/**
 * @ORM\Entity 
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *  "numeric" = "NumericValue",
 *  "varchar" = "VarcharValue",
 *  "text" = "TextValue",
 *  "datetime" = "DatetimeValue",
 *  "file" = "FileValue",
 *  "choice" = "ChoiceValue",
 *  "relationship" = "RelationshipValue",
 *  "map" = "MapValue"
 * })
 * @Assert\Callback(methods={"validate"})
 */
abstract class FieldValue {

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \SpoiledMilk\YoghurtBundle\Entity\Field $field
     * @ORM\ManyToOne(targetEntity="Field", inversedBy="fieldValues")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id") 
     */
    private $field;

    /**
     * @var \SpoiledMilk\YoghurtBundle\Entity\Entity $entity
     * @ORM\ManyToOne(targetEntity="Entity", inversedBy="fieldValues")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id")
     */
    private $entity;

    /**
     * An array of Constraints against whitch the value will be validated
     * @var array
     */
    private $constraints = array();
    
    /**
     * @var integer $position
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getField() {
        return $this->field;
    }

    public function setField(\SpoiledMilk\YoghurtBundle\Entity\Field $field) {
        $this->field = $field;
    }

    public function getEntity() {
        return $this->entity;
    }

    public function setEntity(\SpoiledMilk\YoghurtBundle\Entity\Entity $entity) {
        $this->entity = $entity;
    }

    public function getConstraints() {
        return $this->constraints;
    }

    public function setConstraints($constraints) {
        $this->constraints = $constraints;
    }

    public function addConstraint($constraint) {
        $this->constraints[] = $constraint;
    }
    
    public function getPosition() {
        return $this->position;
    }

    public function setPosition($position) {
        $this->position = $position;
    }

    public function validate(ExecutionContext $context) {
        $validatorFactory = new \Symfony\Component\Validator\ConstraintValidatorFactory();

        foreach ($this->constraints as $constraint) {
            $validator = $validatorFactory->getInstance($constraint);
            $validator->initialize($context);
            $validator->validate($this->getValue(), $constraint);
        }
    }

    abstract public function getValue();

    abstract public function setValue($value);
    
    public function __clone() {
        $this->id = null;
    }
}