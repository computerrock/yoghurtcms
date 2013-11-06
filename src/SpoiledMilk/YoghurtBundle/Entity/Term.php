<?php

namespace SpoiledMilk\YoghurtBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="SpoiledMilk\YoghurtBundle\Repository\TermRepository")
 * @UniqueEntity("term")
 * @UniqueEntity("slug")
 */
class Term {

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $term;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @var \SpoiledMilk\YoghurtBundle\Entity\Vocabulary
     * @ORM\ManyToOne(targetEntity="Vocabulary", inversedBy="terms")
     * @ORM\JoinColumn(name="vocabulary_id", referencedColumnName="id")
     */
    private $vocabulary;

    /**
     * @ORM\ManyToMany(targetEntity="SpoiledMilk\YoghurtBundle\Entity\Entity", mappedBy="terms", fetch="LAZY")
     */
    private $entities;

    /**
     *
     * @var Term
     * @ORM\ManyToOne(targetEntity="SpoiledMilk\YoghurtBundle\Entity\Term", inversedBy="children")
     * @ORM\JoinColumn(name="term_id", referencedColumnName="id")
     */
    private $parent;

    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="SpoiledMilk\YoghurtBundle\Entity\Term", mappedBy="parent", cascade={"all"})
     * @ORM\OrderBy({"term"="ASC"})
     */
    private $children;

    function __construct() {
        $this->children = new ArrayCollection();
    }


    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getTerm() {
        return $this->term;
    }

    public function setTerm($term) {
        $this->term = $term;
    }

    public function getSlug() {
        return $this->slug;
    }

    public function setSlug($slug) {
        $this->slug = $slug;
    }

    public function getVocabulary() {
        return $this->vocabulary;
    }

    public function setVocabulary($vocabulary) {
        $this->vocabulary = $vocabulary;
    }

    public function getEntities() {
        return $this->entities;
    }

    public function setEntities($entities) {
        $this->entities = $entities;
    }

    public function getParent() {
        return $this->parent;
    }

    public function setParent($parent) {
        $this->parent = $parent;
    }

    public function getChildren() {
        return $this->children;
    }

    public function setChildren($children) {
        $this->children = $children;
    }

    public function addChild(\SpoiledMilk\YoghurtBundle\Entity\Term $term) {
        $this->children[] = $term;
    }

    public function removeChild(\SpoiledMilk\YoghurtBundle\Entity\Term $term) {
        $this->children->removeElement($term);
    }

    public function hasChildren() {
        if($this->children->count() > 0)
            return true;
        else
            return false;
    }

    public function getQualifiedTerm() {
        if(!$this->parent)
            return $this->term;
        else
            return $this->parent->getQualifiedTerm() . ' : ' . $this->term;
    }

    public function getIndentedTerm($term = null) {
        $term = isset($term) ? $term : $this;
        if(!$this->parent)
            return $term->term;

        return  '-' . $this->parent->getIndentedTerm($term);
    }

    /**
     * Returns ID of this term, and IDs of all terms that have this term as an
     * ancesstor. Rreturns all ID in this term's tree.
     *
     * @return array
     */
    public function getTermTreeIds() {
        $ret = array($this->id);

        foreach ($this->children as $child) {
            $ret = array_merge($ret, $child->getTermTreeIds());
        }

        return $ret;
    }

}

