<?php

namespace SpoiledMilk\YoghurtBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ChoiceDataTransformer implements DataTransformerInterface {
    
    public function reverseTransform($value) {
        $ret = '';
        
        if(!$value)
            return $ret;
        
        foreach ($value as $val) {
            if($ret)
                $ret .= ',';
            
            $ret .= $val;
        }
            
        return $ret;
    }
    
    public function transform($value) {
        $ret = array();
        
        if(!$value)
            return $ret;
        
        $value = explode(',', $value);
        
        foreach ($value as $val) {
            $ret[] = $val;
        }
            
        return $ret;
    }
}