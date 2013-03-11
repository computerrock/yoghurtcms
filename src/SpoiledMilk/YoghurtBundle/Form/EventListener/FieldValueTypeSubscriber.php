<?php

namespace SpoiledMilk\YoghurtBundle\Form\EventListener;

use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use SpoiledMilk\YoghurtBundle\Services\UtilityService;
use Symfony\Component\Validator\Constraints as Constraints;

class FieldValueTypeSubscriber implements EventSubscriberInterface {

    private $factory;
    
    /**
     * This is set to true if the Field contains not_blank setting
     * @var boolean
     */
    private $isRequired;

    public function __construct(FormFactoryInterface $factory) {
        $this->factory = $factory;
        $this->isRequired = false;
    }

    public static function getSubscribedEvents() {
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(DataEvent $event) {
        $data = $event->getData();
        $form = $event->getForm();

        if ($data === null || !($data instanceof \SpoiledMilk\YoghurtBundle\Entity\FieldValue)) {
            return;
        }

        $fmeta = $data->getField()->getFieldMeta();
        $data->setConstraints($this->getConstraints($fmeta));
        $ftype = null;
        $foptions = array(
            'required' => $this->isRequired,
            'error_bubbling' => false,
            'label' => $data->getField()->getLabel(),
            'attr' => array(
                'title' => $data->getField()->getDescription(),
            ),
        );

        $tmp = $this->getCommonOptions($fmeta);
        $foptions = UtilityService::mergeArrays($foptions, $tmp);
        $dataTransformer = null;

        if ($data instanceof \SpoiledMilk\YoghurtBundle\Entity\VarcharValue) {
            $ftype = 'text';
            
        } else if ($data instanceof \SpoiledMilk\YoghurtBundle\Entity\TextValue) {
            $ftype = 'textarea';
            $tmp = $this->getTextOptions($fmeta);
            $foptions = UtilityService::mergeArrays($foptions, $tmp);
            
        } else if ($data instanceof \SpoiledMilk\YoghurtBundle\Entity\NumericValue) {
            $ftype = 'number';
            $tmp = $this->getNumericOptions($fmeta);
            $foptions = UtilityService::mergeArrays($foptions, $tmp);
            
        } else if ($data instanceof \SpoiledMilk\YoghurtBundle\Entity\DatetimeValue) {
            $ftype = 'datetime';
            $foptions['widget'] = 'single_text';
            $tmp = $this->getDatetimeOptions($fmeta);
            $foptions = UtilityService::mergeArrays($foptions, $tmp);
            
        } else if ($data instanceof \SpoiledMilk\YoghurtBundle\Entity\FileValue) {
            $ftype = 'smfile';
            
        } else if ($data instanceof \SpoiledMilk\YoghurtBundle\Entity\ChoiceValue) {
            $ftype = 'choice';
            $tmp = $this->getChoiceOptions($fmeta);
            $foptions = UtilityService::mergeArrays($foptions, $tmp);

            if (isset($foptions['expanded']) && $foptions['expanded'] 
                    && isset($foptions['multiple'])
                    && $foptions['multiple']) {
                $dataTransformer = new \SpoiledMilk\YoghurtBundle\Form\DataTransformer\ChoiceDataTransformer();
            }
            
        } else if ($data instanceof \SpoiledMilk\YoghurtBundle\Entity\RelationshipValue) {
            $ftype = 'entity';
            $tmp = $this->getRelationshipOptions($fmeta);
            $foptions = UtilityService::mergeArrays($foptions, $tmp);
            
        } else if ($data instanceof \SpoiledMilk\YoghurtBundle\Entity\MapValue) {
            $ftype = 'smMapSingle';
            
            if ($data->getField()->getRepeating()
                    || $this->isMultiMap($fmeta)) {
                $ftype = 'smMapMulti';
            }
            
            $attrs = $form->getAttribute('attr');
            
            if ($attrs && isset($attrs['repeated']))
                $foptions['attr']['repeated'] = true;
            
        }

        $builder = $this->factory->createNamedBuilder('value', $ftype, null, $foptions);
        
        if ($dataTransformer)
            $builder->prependClientTransformer($dataTransformer);

        $form->add($builder->getForm());
    }

    private function getConstraints($fieldMeta) {
        $ret = array();

        foreach ($fieldMeta as $fm) {
            switch ($fm->getMetaKey()) {

                // COMMON
                case 'not_blank':
                    $ret[] = new Constraints\NotBlank();
                    $this->isRequired = true;
                    break;

                case 'true':
                    $ret[] = new Constraints\True();
                    break;

                case 'false':
                    $ret[] = new Constraints\False();
                    break;

                case 'email':
                    $ret[] = new Constraints\Email(array(
                                'checkMX' => ($fm->getMetaValue() == 'checkMX')
                            ));
                    break;

                case 'min_length':
                    $ret[] = new Constraints\MinLength(array('limit' => $fm->getMetaValue()));
                    break;

                case 'max_length':
                    $ret[] = new Constraints\MaxLength(array('limit' => $fm->getMetaValue()));
                    break;

                case 'url':
                    $ret[] = new Constraints\Url(array('protocols' => json_decode($fm->getMetaValue())));
                    break;

                case 'regex':
                    $ret[] = new Constraints\Regex(UtilityService::jsonToArray($fm->getMetaValue()));
                    break;

                // NUMERIC
                case 'max':
                    $ret[] = new Constraints\Max(array('limit' => $fm->getMetaValue()));
                    break;

                case 'min':
                    $ret[] = new Constraints\Min(array('limit' => $fm->getMetaValue()));
                    break;

                // FILE
                case 'file':
                    $ret[] = new Constraints\File(UtilityService::jsonToArray($fm->getMetaValue()));
                    break;

                case 'image':
                    $ret[] = new Constraints\Image(UtilityService::jsonToArray($fm->getMetaValue()));
                    break;
            }
        }

        return $ret;
    }

    /**
     * Returns an array that is to be combined with the field's options array
     * @param array $fieldMeta 
     * @return array
     */
    private function getCommonOptions($fieldMeta) {
        $ret = array();

        foreach ($fieldMeta as $fm) {
            switch ($fm->getMetaKey()) {
                case 'required':
                    $ret['required'] = ($fm->getMetaValue() == '1' || $fm->getMetaValue() == 'true' ? true : false);
                    break;
                case 'trim':
                    $ret['trim'] = ($fm->getMetaValue() == '0' || $fm->getMetaValue() == 'false' ? false : true);
                    break;
            }
        }

        return $ret;
    }

    private function getDatetimeOptions($fieldMeta) {
        return array('format' => 'yyyy-MM-dd HH:mm:ss');
    }

    private function getNumericOptions($fieldMeta) {
        $ret = array();

        foreach ($fieldMeta as $fm) {
            switch ($fm->getMetaKey()) {
                case 'rounding_mode':
                    $ret['rounding_mode'] = $fm->getMetaValue();
                    break;
                case 'precision':
                    $ret['precision'] = $fm->getMetaValue();
                    break;
                case 'grouping':
                    $ret['grouping'] = ($fm->getMetaValue() == '1' || $fm->getMetaValue() == 'true' ? true : false);
                    break;
            }
        }

        return $ret;
    }

    private function getChoiceOptions($fieldMeta) {
        $ret = array();
        $ret['empty_value'] = 'Please select an option';
        $ret['empty_data'] = null;

        foreach ($fieldMeta as $fm) {
            switch ($fm->getMetaKey()) {
                case 'choices':
                    $ret['choices'] = UtilityService::jsonToArray($fm->getMetaValue());
                    break;
                case 'multiple':
                    $ret['multiple'] = ($fm->getMetaValue() == 1 || $fm->getMetaValue() == 'true' ? true : false);
                    break;
                case 'expanded':
                    $ret['expanded'] = ($fm->getMetaValue() == 1 || $fm->getMetaValue() == 'true' ? true : false);
                    break;
                case 'preferred_choices':
                    $ret['preferred_choices'] = UtilityService::jsonToArray($fm->getMetaValue());
                    break;
                case 'empty_value':
                    $ret['empty_value'] = $fm->getMetaValue();
                    break;
                case 'empty_data':
                    $ret['empty_data'] = $fm->getMetaValue();
                    break;
            }
        }

        return $ret;
    }

    private function getRelationshipOptions($fieldMeta) {
        $ret = array();

        $entityType = null;
        $ret['class'] = 'SpoiledMilk\YoghurtBundle\Entity\Entity';
        $ret['empty_value'] = 'Please select an option';
        $ret['empty_data'] = null;

        foreach ($fieldMeta as $fm) {
            switch ($fm->getMetaKey()) {
                case 'multiple':
                    $ret['multiple'] = ($fm->getMetaValue() == 1 || $fm->getMetaValue() == 'true' ? true : false);
                    break;
                case 'expanded':
                    $ret['expanded'] = ($fm->getMetaValue() == 1 || $fm->getMetaValue() == 'true' ? true : false);
                    break;
                case 'empty_value':
                    $ret['empty_value'] = $fm->getMetaValue();
                    break;
                case 'type':
                    $entityType = $fm->getMetaValue();
                    break;
            }
        }

        $ret['query_builder'] = function(\Doctrine\ORM\EntityRepository $er) use ($entityType) {
                    $builder = $er->createQueryBuilder('e')
                            ->innerJoin('e.entityType', 'et');

                    if ($entityType)
                        $builder->andWhere('et.name = :entityType')
                                ->setParameter('entityType', $entityType)
                                ->orderBy('et.position', 'ASC');

                    $builder->addOrderBy('e.position', 'ASC');

                    return $builder;
                };

        return $ret;
    }

    private function getTextOptions($fieldMeta) {
        $ret = array();

        foreach ($fieldMeta as $fm) {
            switch ($fm->getMetaKey()) {
                case 'ckeditor':
                    if ($fm->getMetaValue() == 1 || $fm->getMetaValue() == 'true')
                        $ret['attr']['ckeditor'] = 1;
                    break;
            }
        }

        return $ret;
    }
    
    private function isMultiMap($fieldMeta) {
        foreach ($fieldMeta as $fm) {
            if ($fm->getMetaKey() == 'multi' 
                    && ($fm->getMetaValue() == 'true' || $fm->getMetaValue() == 1) ) {
                return true;
            }
        }
        
        return false;
    }

}