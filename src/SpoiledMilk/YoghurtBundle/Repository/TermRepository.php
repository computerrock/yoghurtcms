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

}
