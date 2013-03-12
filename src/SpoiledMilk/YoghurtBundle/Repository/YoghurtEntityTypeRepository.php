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
    
    /**
     * Reduces the given EntityType's position by swaping it with the EntityType 
     * above it (one with lesser position)
     * 
     * @param \SpoiledMilk\YoghurtBundle\Entity\EntityType $entityType
     */
    public function moveUp(\SpoiledMilk\YoghurtBundle\Entity\EntityType $entityType) {
        
        /* Get EntityTypes with positions less than the given EntityType's 
         * position, ordered so that the closest one is first on the top.
         */
        $dql = 'select e from SpoiledMilkYoghurtBundle:EntityType e 
            where e.position < :pos 
            order by e.position desc';
        
        $this->swapPositions($entityType, $dql);
    }
    
    /**
     * Increases the given Entity's position by swaping it with the Entity below it
     * (Entity with greater position)
     * 
     * @param \SpoiledMilk\YoghurtBundle\Entity\EntityType $entityType
     */
    public function moveDown(\SpoiledMilk\YoghurtBundle\Entity\EntityType $entityType) {
        /* Get EntityTypes with positions less than the given EntityType's 
         * position, ordered so that the closest one is first on the top.
         */
        $dql = 'select e from SpoiledMilkYoghurtBundle:EntityType e 
            where e.position > :pos 
            order by e.position asc';
        
        $this->swapPositions($entityType, $dql);
    }
    
    /**
     * Gets the first EntityType from the DQL query, and swaps it's position 
     * with the given EntityTypes's position
     * 
     * @param \SpoiledMilk\YoghurtBundle\Entity\EntityType $entityType
     * @param string $dqlQuery Query for getting the other EntityType
     */
    private function swapPositions(\SpoiledMilk\YoghurtBundle\Entity\EntityType $entityType, $dqlQuery) {
        $result = $this->_em->createQuery($dqlQuery)
                ->setParameter('pos', $entityType->getPosition())
                ->getResult();
        
        if (!$result)
            return;
        
        $other = $result[0];
        $otherPos = $other->getPosition();
        $other->setPosition($entityType->getPosition());
        $entityType->setPosition($otherPos);
        $this->_em->persist($other);
        $this->_em->persist($entityType);
        $this->_em->flush();
    }

}