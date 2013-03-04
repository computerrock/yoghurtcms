<?php

namespace SpoiledMilk\YoghurtBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;

/**
 * @ORM\Entity
 */
class Publishing {

    /**
     * @var integer $id
     *
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     */
    private $id;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastUpdateDateTime;
    
    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastPublishDateTime;
    
    function __construct($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getLastUpdateDateTime() {
        return $this->lastUpdateDateTime;
    }

    public function setLastUpdateDateTime($lastUpdateDateTime) {
        $this->lastUpdateDateTime = $lastUpdateDateTime;
    }

    public function getLastPublishDateTime() {
        return $this->lastPublishDateTime;
    }

    public function setLastPublishDateTime($lastPublishDateTime) {
        $this->lastPublishDateTime = $lastPublishDateTime;
    }

}