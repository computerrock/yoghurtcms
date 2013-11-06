<?php

namespace SpoiledMilk\YoghurtBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class TermRepository extends EntityRepository {

    /**
     * @param array ID's of vocabularies whose terms we want
     * @return array Resulting array of Terms
     */
    public function fetchFromVocabularies($vocabularies) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('t')
                ->from('SpoiledMilkYoghurtBundle:Term', 't')
                ->add('where', $qb->expr()->in('t.vocabulary', $vocabularies));

        return $qb->getQuery()->getResult();
    }

    /**
     * Generates a slug for the given title. First, it sluggifies the title, then
     * if the slug is already in use by some other entity, it adds a number to
     * the generated value.
     *
     * @param string $entityTitle
     * @return string
     */
    public function generateSlug($entityTitle) {
        $slug = \SpoiledMilk\YoghurtBundle\Services\UtilityService::slugify($entityTitle);
        $dql = 'select t from SpoiledMilkYoghurtBundle:Term t where t.slug = :slug';
        $query = $this->_em
                ->createQuery($dql)
                ->setParameter('slug', $slug);
        $count = sizeof($query->getResult());
        return $slug . ($count ? '-' . ++$count : '');
    }

}
