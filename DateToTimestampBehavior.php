<?php

namespace app\behaviors;
use Yii;
use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\UnknownPropertyException;

/**
 *  Behavior for convert date to timestamp and back
 *  @author Mhthnz
 *  @version 1.0.0
 */
class DateToTimestampBehavior extends Behavior
{
    /**
     *  @var default date format
     */
    private $_format = 'M/d/Y';

    /**
     *  @var date attributes
     */
    private $_attributes = [];


    /**
     *  List of events
     *
     *  @return []
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
        ];
    }

    /**
     *  Setter for date attributes
     */
    public function setAttributes($attributes)
    {
        if (is_array($attributes)) {
            if (!count($attributes)) {
                throw new UnknownPropertyException("Attributes not found.");
            }
            foreach($attributes as $attribute) {
                if (is_array($attribute)) { 
                    if (!array_key_exists('attribute', $attribute)) {
                        throw new UnknownPropertyException("Attribute is required.");
                    }
                }
            }
            $this->_attributes = $attributes;
        } else if(!empty($attributes)) {
            $this->_attributes = [$attributes];
        } else {
            throw new UnknownPropertyException("Attribute is empty.");
        }
    }

    /**
     *  Setter for format
     */
    public function setFormat($format)
    {
        $this->_format = $format;
    }

    /**
     *  Convert from date to timestamp
     */
    public function beforeSave($event)
    {
        foreach($this->_attributes as $attribute) {
            $att = "";
            $format = $this->_format;
            if (is_array($attribute)) {
                $att = $attribute['attribute'];
                if (isset($attribute['format'])) {
                    $format = $attribute['format'];
                }
            } else {
                $att = $attribute;
            }
            if (!empty($this->owner->$att) && !$this->isTimestamp($this->owner->$att)) {
                $this->owner->$att = $this->convertToTimestamp($this->owner->$att, $format);    
            }
        }
    }

    /**
     *  Convert timestamp to date by format
     */
    public function afterSave($event)
    {
        foreach($this->_attributes as $attribute) {
            $att = "";
            $format = $this->_format;
            if (is_array($attribute)) {
                $att = $attribute['attribute'];
                if (isset($attribute['format'])) {
                    $format = $attribute['format'];
                }
            } else {
                $att = $attribute;
            }
            if (!empty($this->owner->$att) && $this->isTimestamp($this->owner->$att)) {
                $this->owner->$att = $this->convertToDate($this->owner->$att, $format);    
            }
        }
    }

    /**
     *  Convert timestamp to date by format
     */
    public function afterFind($event)
    {
        return $this->afterSave($event);
    }

    /**
     *  Check string is timestamp  
     *
     *  @return boolean
     */
    private function isTimestamp($string)
    {
        return ( is_numeric($string) && (int)$string == $string );
    }

    /**
     *  Convert date to timestamp by format
     *  
     *  @return int timestamp
     */
    private function convertToTimestamp($date, $format)
    {
        $date = date_create_from_format($format, $date);
        return $date->getTimestamp();
    }

    /**
     *  Convert timestamp to date
     *
     *  @return string date
     */
    private function convertToDate($timestamp, $format)
    {
        return date($format, $timestamp);
    }
}   
