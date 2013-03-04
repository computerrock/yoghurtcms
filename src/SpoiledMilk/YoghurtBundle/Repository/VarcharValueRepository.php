<?php

namespace SpoiledMilk\YoghurtBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use SpoiledMilk\YoghurtBundle\Entity as YE;

class VarcharValueRepository extends EntityRepository {

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
    
    public function fetchByNameAndValue($fieldName, $fieldValue) {
        $builder = $this->_em->createQueryBuilder()
                ->select('fv')
                ->from('SpoiledMilkYoghurtBundle:VarcharValue', 'fv')
                ->innerJoin('fv.field', 'f')
                ->andWhere('f.name = :fname')
                ->andWhere('fv.value = :fval')
        ;

        $builder->setParameter('fname', $fieldName)
                ->setParameter('fval', $fieldValue)
        ;

        return $builder->getQuery()->getResult();
    }

}
