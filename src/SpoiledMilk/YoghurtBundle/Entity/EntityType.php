<?php

namespace SpoiledMilk\YoghurtBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * @ORM\Entity(repositoryClass="SpoiledMilk\YoghurtBundle\Repository\YoghurtEntityTypeRepository")
 * @ORM\Table(uniqueConstraints={
 *      @ORM\UniqueConstraint(name="entity_type_uq", columns={"name", "slug"})
 * })
 * @ORM\HasLifeCycleCallbacks
 * @UniqueEntity("name")
 * @UniqueEntity("slug")
 */
class EntityType {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $name;
    
    /**
     * @var string $name
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $namePlural;

    /**
     * @var string $slug
     * @ORM\Column(name="slug", type="string", length=255, nullable=false)
     */
    private $slug;

    /**
     * @var integer $order
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position;

    /**
     * @ORM\OneToMany(targetEntity="Field", mappedBy="entityType", cascade={"all"})
     * @ORM\OrderBy({"position"="ASC"})
     */
    private $fields;

    /**
     * @ORM\OneToMany(targetEntity="Entity", mappedBy="entityType", cascade={"all"})
     * @ORM\OrderBy({"position"="ASC"})
     */
    private $entities;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="EntityTypeVocabulary", mappedBy="entityType", cascade={"all"})
     */
    private $entityTypeVocabularies;

    public function __construct() {
        $this->fields = new ArrayCollection();
        $this->entities = new ArrayCollection();
    }

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
    
    public function getNamePlural() {
        return $this->namePlural;
    }

    public function setNamePlural($namePlural) {
        $this->namePlural = $namePlural;
    }

    public function getSlug() {
        return $this->slug;
    }

    public function setSlug($slug) {
        $this->slug = $slug;
    }

    public function getPosition() {
        return $this->position;
    }

    public function setPosition($position) {
        $this->position = $position;
    }

    public function getFields() {
        return $this->fields;
    }

    public function setFields($fields) {
        $this->fields = $fields;
    }

    public function addField(\SpoiledMilk\YoghurtBundle\Entity\Field $field) {
        $this->fields->add($field);
    }

    public function getEntities() {
        return $this->entities;
    }

    public function setEntities($entities) {
        $this->entities = $entities;
    }

    public function addEntity(\SpoiledMilk\YoghurtBundle\Entity\Entity $entity) {
        $this->entities->add($entity);
    }

    public function getMaxFieldPosition() {
        $max = 0;

        foreach ($this->fields as $field) {
            if ($max < $field->getPosition()) {
                $max = $field->getPosition();
            }
        }

        return $max;
    }
    
    public function getEntityTypeVocabularies() {
        return $this->entityTypeVocabularies;
    }

    public function setEntityTypeVocabularies($entityTypeVocabularies) {
        $this->entityTypeVocabularies = $entityTypeVocabularies;
    }

    public function getMaxEntityPosition() {
        $max = 0;

        foreach ($this->entities as $entity) {
            if ($max < $entity->getPosition()) {
                $max = $entity->getPosition();
            }
        }

        return $max;
    }

    /**
     * @ORM\PrePersist 
     * @ORM\PreUpdate
     */
    public function prePersist() {
        if (!$this->slug) {
            $this->slug = urlencode(strtolower($this->name));
        }
        
        if (!$this->namePlural) {
            $this->namePlural = $this->name;
        }
    }

}