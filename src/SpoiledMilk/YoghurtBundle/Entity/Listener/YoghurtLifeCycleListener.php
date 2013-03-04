<?php

namespace SpoiledMilk\YoghurtBundle\Entity\Listener;

use SpoiledMilk\YoghurtBundle\Entity as YEntity;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;

class YoghurtLifeCycleListener {

    public function preUpdate(LifecycleEventArgs $args) {
        $entity = $args->getEntity();

        if ($entity instanceof YEntity\EntityType) {
            $this->entityTypePerPersistUpdate($entity, $args->getEntityManager());
        } else if ($entity instanceof YEntity\FieldValue) {
            $this->fieldValuePrePersistUpdate($entity, $args->getEntityManager());
        }
    }

    public function prePersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();

        if ($entity instanceof YEntity\EntityType) {
            $this->entityTypePerPersistUpdate($entity, $args->getEntityManager());
        } else if ($entity instanceof YEntity\FieldValue) {
            $this->fieldValuePrePersistUpdate($entity, $args->getEntityManager());
        }
    }
    
    public function onFlush(OnFlushEventArgs $args) {
        $uow = $args->getEntityManager()->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof YEntity\Field) {
                
                if($this->fieldOnFlush($entity, $uow))
                    $uow->computeChangeSets();
            }
        }
    }
    
    private function entityTypePerPersistUpdate($entityType, $entityManager) {
        if (!$entityType->getPosition()) {
            $pos = $entityManager
                    ->getRepository('SpoiledMilkYoghurtBundle:EntityType')
                    ->getMaxPosition();
            $entityType->setPosition($pos + 1);
        }
    }

    private function fieldValuePrePersistUpdate($fieldValue, $entityManager) {
        if (!$fieldValue->getPosition()) {
            $pos = 1000 * $fieldValue->getField()->getPosition()
                    + $fieldValue->getEntity()->countFieldValues() + 1;
            $fieldValue->setPosition($pos);
        }
    }
    
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
