<?php

namespace SpoiledMilk\YoghurtBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="SpoiledMilk\YoghurtBundle\Repository\FileValueRepository")
 */
class FileValue extends FieldValue {
    
    /**
     * @var string $value
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $value;
    
    /**
     * @var string $value
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $prefix;

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }
    
    public function getPrefix() {
        return $this->prefix;
    }

    public function setPrefix($prefix) {
        $this->prefix = $prefix;
    }

}

?>
