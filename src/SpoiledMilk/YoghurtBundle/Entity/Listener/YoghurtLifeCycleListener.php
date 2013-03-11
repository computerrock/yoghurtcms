<?php

namespace SpoiledMilk\YoghurtBundle\Entity\Listener;

use SpoiledMilk\YoghurtBundle\Entity as YEntity;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;

class YoghurtLifeCycleListener {

    /**
     * Used to set position value on EntityType and FieldValue instances, if 
     * they're not already set.
     * 
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args) {
        $entity = $args->getEntity();

        if ($entity instanceof YEntity\EntityType) {
            $this->entityTypePerPersistUpdate($entity, $args->getEntityManager());
        } else if ($entity instanceof YEntity\FieldValue) {
            $this->fieldValuePrePersistUpdate($entity, $args->getEntityManager());
        }
    }

    /**
     * Used to set position value on EntityType and FieldValue instances, if 
     * they're not already set.
     * 
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();

        if ($entity instanceof YEntity\EntityType) {
            $this->entityTypePerPersistUpdate($entity, $args->getEntityManager());
        } else if ($entity instanceof YEntity\FieldValue) {
            $this->fieldValuePrePersistUpdate($entity, $args->getEntityManager());
        }
    }
    
    /**
     * Handles changes in Field instances. Position and type.
     * 
     * @param \Doctrine\ORM\Event\OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args) {
        $uow = $args->getEntityManager()->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof YEntity\Field) {
                
                if($this->fieldOnFlush($entity, $uow))
                    $uow->computeChangeSets();
            }
        }
    }
    
    /**
     * If EntityType instance has no position, generate and set it.
     * 
     * @param \SpoiledMilk\YoghurtBundle\Entity\EntityType $entityType
     * @param type $entityManager
     */
    private function entityTypePerPersistUpdate($entityType, $entityManager) {
        if (!$entityType->getPosition()) {
            $pos = $entityManager
                    ->getRepository('SpoiledMilkYoghurtBundle:EntityType')
                    ->getMaxPosition();
            $entityType->setPosition($pos + 1);
        }
    }

    /**
     * If FieldValue instance nas no position, generate and set it.
     * 
     * @param SpoiledMilk\YoghurtBundle\Entity\FieldValue $fieldValue
     * @param type $entityManager
     */
    private function fieldValuePrePersistUpdate($fieldValue, $entityManager) {
        if (!$fieldValue->getPosition()) {
            $pos = 1000 * $fieldValue->getField()->getPosition()
                    + $fieldValue->getEntity()->countFieldValues() + 1;
            $fieldValue->setPosition($pos);
        }
    }
    
    /**
     * Handles changes in position (repositions FieldValues) and type (tries to
     * convert existing data to the new type) on a Field instance.
     * 
     * @param \SpoiledMilk\YoghurtBundle\Entity\Field $field
     * @param \Doctrine\ORM\UnitOfWork $uow
     * 
     * @return boolean True if additional changes were made in this method, 
     * false otherwise
     */
    private function fieldOnFlush(YEntity\Field $field, UnitOfWork $uow) {
        $additionalChanges = false;
        $changeSet = $uow->getEntityChangeSet($field);
        
        if (isset($changeSet['position'])) {
            $additionalChanges = true;
            $oldPosition = $changeSet['position'][0] * 1000;
            $newPosition = $changeSet['position'][1] * 1000;

            if ($oldPosition != $newPosition) {
                foreach ($field->getFieldValues() as $fieldValue) {
                    $fvPos = $fieldValue->getPosition();
                    $fieldValue->setPosition($fvPos - $oldPosition + $newPosition);
                    $uow->scheduleForUpdate($fieldValue);
                }
                
            }
        }
        
        if (isset($changeSet['fieldType'])) {
            $this->changeFieldType($field, $changeSet['fieldType'][1], $uow);
            $additionalChanges = true;
        }
        
        return $additionalChanges;
    }
    
    /**
     * Handles changes of field type. Uses ValueConverter to try and preserve 
     * values already set.
     * 
     * @param \SpoiledMilk\YoghurtBundle\Entity\Field $field
     * @param \SpoiledMilk\YoghurtBundle\Entity\FieldType $newType
     * @param \Doctrine\ORM\UnitOfWork $uow
     */
    private function changeFieldType(YEntity\Field $field, YEntity\FieldType $newType, UnitOfWork $uow) {
        foreach ($field->getFieldValues() as $fieldValue) {
            $className = 'SpoiledMilk\YoghurtBundle\Entity\\' . $newType->getClassName();
            $newFieldValue = new $className;
            $newFieldValue->setEntity($fieldValue->getEntity());
            $newFieldValue->setField($field);
            $newFieldValue->setPosition($fieldValue->getPosition());
            $newFieldValue->setValue(YEntity\Converter\ValueConverter::convertValue($fieldValue, $newFieldValue));
            
            $uow->scheduleForDelete($fieldValue);
            $uow->scheduleForInsert($newFieldValue);
        }
    }

}
