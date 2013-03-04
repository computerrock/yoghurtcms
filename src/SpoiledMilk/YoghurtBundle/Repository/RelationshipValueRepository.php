<?php

namespace SpoiledMilk\YoghurtBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use SpoiledMilk\YoghurtBundle\Entity as YE;

class RelationshipValueRepository extends EntityRepository {

    public function fetchAllRelatedTo(YE\Entity $entity) {
        $query = $this->_em->createQueryBuilder()
                ->select('rv')
                ->from('SpoiledMilkYoghurtBundle:RelationshipValue', 'rv')
                ->where('rv.value = :rel')
                ->setParameter('rel', $entity)
                ->getQuery()
                ->setFetchMode('\SpoiledMilk\YoghurtBundle\Entity\Entity', 'rv.entity', \Doctrine\ORM\Mapping\ClassMetadata::FETCH_EAGER)
        ;
        
        return $query->getResult();
    }

}
