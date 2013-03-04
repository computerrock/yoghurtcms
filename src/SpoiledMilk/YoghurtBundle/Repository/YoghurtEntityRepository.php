<?php

namespace SpoiledMilk\YoghurtBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class YoghurtEntityRepository extends EntityRepository {

    /**
     * @param string $slug Entity's slug
     * @param string|EntityType $entityType EntityType object or string representig it's slug
     * @return array
     */
    public function fetchOneBySlugAndType($slug, $entityType) {
        $qb = $this->_em->createQueryBuilder()
                ->select('e')
                ->from('SpoiledMilkYoghurtBundle:Entity', 'e')
                ->innerJoin('e.entityType', 'et')
                ->where('e.slug = :slug')
                ->setParameter('slug', $slug)
                ->setMaxResults(1);

        if ($entityType instanceof \SpoiledMilk\YoghurtBundle\Entity\EntityType) {
            $qb->andWhere('et.id = :etid')
                    ->setParameter('etid', $entityType->getId());
        } else {
            $qb->andWhere('et.slug = :etSlug')
                    ->setParameter('etSlug', $entityType);
        }

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            return null;
        }
    }

    /**
     * @param String|EntityType $entityType EntityType object or string representig it's slug
     * @return array
     */
    public function fetchByType($entityType) {
        $qb = $this->_em->createQueryBuilder()
                ->select('e')
                ->from('SpoiledMilkYoghurtBundle:Entity', 'e')
                ->innerJoin('e.entityType', 'et');

        if ($entityType instanceof \SpoiledMilk\YoghurtBundle\Entity\EntityType) {
            $qb->where('et.id = :etid')
                    ->setParameter('etid', $entityType->getId());
        } else {
            $qb->where('et.slug = :etSlug')
                    ->setParameter('etSlug', $entityType);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array|integer $entityTypeId Array of EntityTypes ids, or a single EntityType id
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderForPaginated($entityTypeId = null) {
        $builder = $this->_em->createQueryBuilder()
                ->select('e')
                ->from('SpoiledMilkYoghurtBundle:Entity', 'e')
                ->innerJoin('e.entityType', 'et');

        if ($entityTypeId != null) {
            if (is_array($entityTypeId)) {
                $builder
                        ->andWhere($builder->expr()->in('et.id', $entityTypeId))
                        ->addOrderBy('et.position', 'ASC');
            } else {
                $builder
                        ->andWhere('et.id = :entityTypeId')
                        ->setParameter('entityTypeId', $entityTypeId);
            }
        }

        $builder->addOrderBy('e.position', 'ASC');
        return $builder;
    }

    /**
     * @param array $idArray
     * @return array
     */
    public function fetchMultipleByIds($idArray) {
        $builder = $this->_em->createQueryBuilder();

        return $builder
                        ->select('e')
                        ->from('SpoiledMilkYoghurtBundle:Entity', 'e')
                        ->where($builder->expr()->in('e.id', $idArray))
                        ->getQuery()
                        ->getResult();
    }
    
    /**
     * @param integer $status
     * @return array
     */
    public function fetchAllByStatus($status) {
        if (!is_int($status))
            throw new \UnexpectedValueException('status must be an integer, "' . $status . '" given.' );
        
        return $this->_em->createQueryBuilder()
                ->select('e')
                ->from('SpoiledMilkYoghurtBundle:Entity', 'e')
                ->where('e.status = :status')
                ->setParameter('status', $status)
                ->getQuery()
                ->getResult();
    }

}