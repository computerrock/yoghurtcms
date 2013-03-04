<?php

namespace SpoiledMilk\YoghurtBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="SpoiledMilk\YoghurtBundle\Repository\RelationshipValueRepository")
 */
class RelationshipValue extends FieldValue {

    /**
     * @var string $value
     *
     * @ORM\ManyToOne(targetEntity="Entity", cascade={"persist"})
     * @ORM\JoinColumn(name="value", referencedColumnName="id")
     */
    private $value;

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }

}