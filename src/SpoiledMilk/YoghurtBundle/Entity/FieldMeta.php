<?php

namespace SpoiledMilk\YoghurtBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class FieldMeta
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $metaKey
     *
     * @ORM\Column(name="meta_key", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $metaKey;

    /**
     * @var string $metaValue
     *
     * @ORM\Column(name="meta_value", type="text")
     * @Assert\NotBlank()
     */
    private $metaValue;

    /**
     * @var Field
     *
     * @ORM\ManyToOne(targetEntity="Field", inversedBy="fieldMeta")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id")
     */
    private $field;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getMetaKey() {
        return $this->metaKey;
    }

    public function setMetaKey($metaKey) {
        $this->metaKey = $metaKey;
    }

    public function getMetaValue() {
        return $this->metaValue;
    }

    public function setMetaValue($metaValue) {
        $this->metaValue = $metaValue;
    }

    public function getField() {
        return $this->field;
    }

    public function setField($field) {
        $this->field = $field;
    }


}