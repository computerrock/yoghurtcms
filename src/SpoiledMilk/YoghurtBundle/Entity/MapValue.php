<?php

namespace SpoiledMilk\YoghurtBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class MapValue extends FieldValue {

    /**
     * @var string $value
     *
     * @ORM\Column(name="value", type="text", nullable=true)
     */
    private $value;
    
    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }

}