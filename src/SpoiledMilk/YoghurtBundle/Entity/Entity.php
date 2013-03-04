<?php

namespace SpoiledMilk\YoghurtBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;

/**
 * @ORM\Entity(repositoryClass="SpoiledMilk\YoghurtBundle\Repository\YoghurtEntityRepository")
 * @ORM\Table(
 *      uniqueConstraints={@ORM\UniqueConstraint(name="entity_uq", columns={"entity_type_id", "slug"})},
 *      indexes={@ORM\Index(name="status_idx", columns={"status"})}
 * )
 * @ORM\HasLifeCycleCallbacks
 * @UniqueEntity(fields={"entityType", "slug"}, message="The slug you entered is already in use.")
 * @Assert\Callback(methods={"validate"})
 */
class Entity {
    
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;
    const STATUS_TEST = 2;

    /**
     * @var integer $id
     *
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank
     */
    private $title;
    
    /**
     * @var string $slug *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     *
     * @var integer $position
     * @ORM\Column(type="integer", nullable=false)
     */
    private $position;
    
    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    private $modified;
    
    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @var \SpoiledMilk\YoghurtBundle\Entity\EntityType
     *
     * @ORM\ManyToOne(targetEntity="EntityType", inversedBy="entities")
     * @ORM\JoinColumn(name="entity_type_id", referencedColumnName="id")
     */
    private $entityType;

    /**
     * @ORM\OneToMany(targetEntity="FieldValue", mappedBy="entity", cascade={"all"})
     * @ORM\OrderBy({"position"="ASC"})
     */
    private $fieldValues;

    /**
     * @ORM\ManyToMany(targetEntity="Term", inversedBy="entities")
     */
    private $terms;

    public function __construct() {
        $this->fieldValues = new ArrayCollection();
        $this->terms = new ArrayCollection();
        $this->status = Entity::STATUS_ENABLED;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }
    
    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
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
    
    public function getModified() {
        return $this->modified;
    }

    public function setModified($modified) {
        $this->modified = $modified;
    }
    
    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getEntityType() {
        return $this->entityType;
    }

    public function setEntityType($entityType) {
        $this->entityType = $entityType;
    }

    public function getFieldValues() {
        return $this->fieldValues;
    }

    public function setFieldValues($fieldValues) {
        $this->fieldValues = $fieldValues;
    }

    public function addFieldValue(\SpoiledMilk\YoghurtBundle\Entity\FieldValue $val) {
        $this->fieldValues->add($val);
        $val->setEntity($this);
    }
    
    public function countFieldValues() {
        return $this->fieldValues->count();
    }
    
    public function getFieldValue($name) {
        foreach ($this->fieldValues as $value) {
            if ($value->getField()->getName() == $name) {
                return $value;
            }
        }
        
        return null;
    }

    public function getTerms() {
        return $this->terms;
    }

    public function setTerms($terms) {
        $this->terms = $terms;
    }

    public function addTerm(\SpoiledMilk\YoghurtBundle\Entity\Term $term) {
        $this->terms->add($term);
    }
    
    public function removeTerm(\SpoiledMilk\YoghurtBundle\Entity\Term $term) {
        $this->terms->removeElement($term);
    }

    /**
     * @ORM\PrePersist 
     * @ORM\PreUpdate
     */
    public function prePersist() {
        if (!$this->position) {
            $this->position = $this->entityType->getMaxEntityPosition() + 1;
        }
        
        $this->modified = new \DateTime();
    }

    public function __toString() {
        return $this->slug;
    }

    public function validate(ExecutionContext $context) {
        // Get all EntityTypeVocabularies
        $etvs = $this->entityType->getEntityTypeVocabularies();

        // Get all allowed Vocabularies
        $allowedVoc = array();

        foreach ($etvs as $etv) {
            $allowedVoc[] = $etv->getVocabulary();

            if ($etv->getMandatory()) {
                // Check if you have at least one Term from this Vocabulary
                $haveTerm = false;

                foreach ($this->terms as $entityTerm) {
                    foreach ($etv->getVocabulary()->getTerms() as $vocTerm) {
                        if ($entityTerm->getId() == $vocTerm->getId()) {
                            $haveTerm = true;
                            break;
                        }
                    }

                    if ($haveTerm) {
                        break;
                    }
                }

                if (!$haveTerm) {
                    $context->addViolation('You must add at least one term from '
                            . $etv->getVocabulary()->getName()
                            . ' vocabulary', array(), null);
                }
            }
        }

        // Check if all the attached Terms are from the allowed Vocabularies
        foreach ($this->terms as $term) {
            if (!in_array($term->getVocabulary(), $allowedVoc)) {
                $context->addViolation('The term "'
                        . $term->getTerm()
                        . '" is not from any of the attached vocabularies. Please remove it.', array(), null);
            }
        }
    }

}