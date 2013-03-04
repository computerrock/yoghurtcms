<?php

namespace SpoiledMilk\YoghurtBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class YoghurtEntityTypeRepository extends EntityRepository {

    public function fetchAllSorted() {
        $query = $this->_em->createQueryBuilder()
                ->select('et')
                ->from('SpoiledMilkYoghurtBundle:EntityType', 'et')
                ->orderBy('et.position', 'ASC')
                ->getQuery();
        return $query->getResult();
    }

    public function fetchByIdSorted($id) {
        $qb = $this->_em->createQueryBuilder()
                ->select('et')
                ->from('SpoiledMilkYoghurtBundle:EntityType', 'et')
                ->orderBy('et.position', 'ASC');

        if (is_array($id)) {
            $qb->where($qb->expr()->in('et.id', $id));
        } else {
            $qb->where('et.id = :etId')
                    ->setParameter('etId', $id);
        }
        
        return $qb->getQuery()->getResult();
    }

    public function getMaxPosition() {
        $query = $this->_em->createQueryBuilder()
                ->select('et')
                ->from('SpoiledMilkYoghurtBundle:EntityType', 'et')
                ->orderBy('et.position', 'DESC')
                ->setMaxResults(1)
                ->getQuery();
        $ret = 0;

        try {
            $ret = $query->getSingleResult()->getPosition();
        } catch (NoResultException $e) {
            $ret = 0;
        }

        return $ret;
    }

}