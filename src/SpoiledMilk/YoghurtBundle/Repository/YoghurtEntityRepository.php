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
            throw new \UnexpectedValueException('status must be an integer, "' . $status . '" given.');

        return $this->_em->createQueryBuilder()
                        ->select('e')
                        ->from('SpoiledMilkYoghurtBundle:Entity', 'e')
                        ->where('e.status = :status')
                        ->setParameter('status', $status)
                        ->getQuery()
                        ->getResult();
    }

    /**
     * Reduces the given Entity's position by swaping it with the Entity above it
     * (Entity with lesser position)
     * 
     * @param \SpoiledMilk\YoghurtBundle\Entity\Entity $entity
     */
    public function moveUp(\SpoiledMilk\YoghurtBundle\Entity\Entity $entity) {

        /* Get Entities of the same Type, with positions less than the given
         * Entity's position, ordered so that the closes one is firs on the top.
         */
        $dql = 'select e from SpoiledMilkYoghurtBundle:Entity e 
            join e.entityType et 
            where e.position < :pos 
            and et.id = :etid
            order by e.position desc';

        $this->swapPositions($entity, $dql);
    }

    /**
     * Increases the given Entity's position by swaping it with the Entity below it
     * (Entity with greater position)
     * 
     * @param \SpoiledMilk\YoghurtBundle\Entity\Entity $entity
     */
    public function moveDown(\SpoiledMilk\YoghurtBundle\Entity\Entity $entity) {
        /* Get Entities of the same Type, with positions less than the given
         * Entity's position, ordered so that the closes one is firs on the top.
         */
        $dql = 'select e from SpoiledMilkYoghurtBundle:Entity e 
            join e.entityType et 
            where e.position > :pos 
            and et.id = :etid
            order by e.position asc';

        $this->swapPositions($entity, $dql);
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
        $dql = 'select e from SpoiledMilkYoghurtBundle:Entity e where e.slug = :slug';
        $query = $this->_em
                ->createQuery($dql)
                ->setParameter('slug', $slug);
        $count = sizeof($query->getResult());
        return $slug . ($count ? '-' . ++$count : '');
    }
    
    /**
     * Returns the date and time of the last modification in the database's 
     * preffered format. For MySql it's: year-monht-day hour:minute:second
     * 
     * @return string String (year-monht-day hour:minute:second) or null if no
     * entities exist in the database.
     */
    public function getLastModifiedDateTime() {
        $dql = 'select max(e.modified) from SpoiledMilkYoghurtBundle:Entity e';
        return $this->_em->createQuery($dql)->getSingleScalarResult();
    }

    /**
     * Gets the first Entity from the DQL query, and swaps it's position with
     * the given Entity's position
     * 
     * @param \SpoiledMilk\YoghurtBundle\Entity\Entity $entity
     * @param string $dqlQuery Query for getting the other Entity
     */
    private function swapPositions(\SpoiledMilk\YoghurtBundle\Entity\Entity $entity, $dqlQuery) {
        $result = $this->_em->createQuery($dqlQuery)
                ->setParameter('pos', $entity->getPosition())
                ->setParameter('etid', $entity->getEntityType()->getId())
                ->getResult();

        if (!$result)
            return;

        $other = $result[0];
        $otherPos = $other->getPosition();
        $other->setPosition($entity->getPosition());
        $entity->setPosition($otherPos);
        $this->_em->persist($other);
        $this->_em->persist($entity);
        $this->_em->flush();
    }

}