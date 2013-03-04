<?php

namespace SpoiledMilk\YoghurtBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class NumericValue extends FieldValue {

    /**
     * @var decimal $value
     *
     * @ORM\Column(name="value", type="decimal", nullable=true, precision=30, scale=10)
     */
    private $value;

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }

}