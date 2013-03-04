<?php

namespace SpoiledMilk\YoghurtBundle\Entity\Converter;

use SpoiledMilk\YoghurtBundle\Entity as YE;

class ValueConverter {

    public static function convertValue(YE\FieldValue $value, $toType) {
        try {
            if ($value instanceof YE\ChoiceValue) {
                return @self::fromChoice($value, $toType);
            } else if ($value instanceof YE\DatetimeValue) {
                return @self::fromDatetime($value, $toType);
            } else if ($value instanceof YE\FileValue) {
                return @self::fromFile($value, $toType);
            } else if ($value instanceof YE\MapValue) {
                return @self::fromMap($value, $toType);
            } else if ($value instanceof YE\NumericValue) {
                return @self::fromNumeric($value, $toType);
            } else if ($value instanceof YE\RelationshipValue) {
                return @self::fromRelationship($value, $toType);
            } else if ($value instanceof YE\TextValue) {
                return @self::fromText($value, $toType);
            } else if ($value instanceof YE\VarcharValue) {
                return @self::fromVarchar($value, $toType);
            }
        } catch (\Exception $e) {
            
        }

        return null;
    }

    private static function fromChoice(YE\ChoiceValue $value, $toType) {

        if ($toType instanceof YE\DatetimeValue)
            return new \DateTime($value->getValue());
        if ($toType instanceof YE\NumericValue)
            return is_numeric($value->getValue()) ? $value->getValue() : null;
        if ($toType instanceof YE\TextValue)
            return $value->getValue();
        if ($toType instanceof YE\VarcharValue)
            return $value->getValue();

        return null;
    }

    private static function fromDatetime(YE\DatetimeValue $value, $toType) {

        if ($toType instanceof YE\DatetimeValue)
            return $value->getValue();
        if ($toType instanceof YE\NumericValue)
            return $value->getValue() ? $value->getValue()->getTimestamp() : null;
        if ($toType instanceof YE\TextValue)
            return $value->getValue() ? $value->getValue()->format(\DateTime::COOKIE) : null;
        if ($toType instanceof YE\VarcharValue)
            return $value->getValue() ? $value->getValue()->format(\DateTime::COOKIE) : null;

        return null;
    }

    private static function fromFile(YE\FileValue $value, $toType) {

        if ($toType instanceof YE\TextValue)
            return $value->getValue() ? $value->getPrefix() . $value->getValue() : null;
        if ($toType instanceof YE\VarcharValue)
            return $value->getValue() ? substr($value->getPrefix() . $value->getValue(), 0, 255) : null;

        return null;
    }

    private static function fromMap(YE\MapValue $value, $toType) {

        if ($toType instanceof YE\TextValue)
            return $value->getValue();
        if ($toType instanceof YE\VarcharValue)
            return $value->getValue() ? substr($value->getValue(), 0, 255) : null;

        return null;
    }

    private static function fromNumeric(YE\NumericValue $value, $toType) {

        if ($toType instanceof YE\ChoiceValue)
            return $value->getValue() ? round($value->getValue()) : null;
        if ($toType instanceof YE\DatetimeValue)
            return $value->getValue() ? new \DateTime(round($value->getValue())) : null;
        if ($toType instanceof YE\TextValue)
            return $value->getValue();
        if ($toType instanceof YE\VarcharValue)
            return $value->getValue();

        return null;
    }

    private static function fromRelationship(YE\RelationshipValue $value, $toType) {

        if ($toType instanceof YE\ChoiceValue)
            return $value->getValue() ? $value->getValue()->getId() : null;
        if ($toType instanceof YE\NumericValue)
            return $value->getValue() ? $value->getValue()->getId() : null;
        if ($toType instanceof YE\TextValue)
            return $value->getValue() ? $value->getValue()->getId() : null;
        if ($toType instanceof YE\VarcharValue)
            return $value->getValue() ? $value->getValue()->getId() : null;

        return null;
    }

    private static function fromText(YE\TextValue $value, $toType) {

        if ($toType instanceof YE\ChoiceValue)
            return $value->getValue() ? substr($value->getValue(), 0, 255) : null;
        if ($toType instanceof YE\DatetimeValue)
            return $value->getValue() ? new \DateTime($value->getValue()) : null;
        if ($toType instanceof YE\FileValue)
            return $value->getValue() ? substr($value->getValue(), 0, 255) : null;
        if ($toType instanceof YE\MapValue)
            return $value->getValue();
        if ($toType instanceof YE\NumericValue)
            return $value->getValue() && is_numeric($value->getValue()) ? $value->getValue() : null;
        if ($toType instanceof YE\VarcharValue)
            return $value->getValue() ? substr($value->getValue(), 0, 255) : null;

        return null;
    }

    private static function fromVarchar(YE\VarcharValue $value, $toType) {

        if ($toType instanceof YE\ChoiceValue)
            return $value->getValue();
        if ($toType instanceof YE\DatetimeValue)
            return $value->getValue() ? new \DateTime($value->getValue()) : null;
        if ($toType instanceof YE\FileValue)
            return $value->getValue();
        if ($toType instanceof YE\MapValue)
            return $value->getValue();
        if ($toType instanceof YE\NumericValue)
            return $value->getValue() && is_numeric($value->getValue()) ? $value->getValue() : null;
        if ($toType instanceof YE\TextValue)
            return $value->getValue();

        return null;
    }

}
