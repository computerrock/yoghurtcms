<?php

namespace SpoiledMilk\YoghurtBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class DatetimeValue extends FieldValue
{

    /**
     * @var datetime $value
     *
     * @ORM\Column(name="value", type="datetime", nullable=true)
     */
    private $value;

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }

}