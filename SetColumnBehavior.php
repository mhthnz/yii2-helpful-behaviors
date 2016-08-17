<?php

namespace app\behaviors;
use Yii;
use yii\db\ActiveRecord;
use yii\base\Behavior;

/**
 *  Behavior for working with SET type MySQL
 *  @author Mhthnz
 *  @version 1.0.0
 */
class SetColumnBehavior extends Behavior
{
    /**
     *  @var attributes of type SET
     */
    private $_attributes = [];


    /**
     *  List of events
     *  @return []
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
        ];
    }

    /**
     *  Set model attributes
     */
    public function setAttributes($value)
    {
        if (!is_array($value)) {
            $attribute = $value;
            $value = [$attribute];
        }
        $this->_attributes = $value;
    }

    /**
     *  Conver from array to string
     */
    public function beforeSave($event)
    {
        foreach($this->_attributes as $attribute) {
            if (is_array($this->owner->{$attribute})) {
                $value = "";
                $value .= implode(',', $this->owner->{$attribute});
                $this->owner->{$attribute} = $value;
            }
        }
    }

    /**
     *  Convert string to array
     */
    public function afterFind($event)
    {
        foreach($this->_attributes as $attribute) {
            if (!is_array($this->owner->{$attribute})) {
                $value = [];
                foreach(explode(',', $this->owner->{$attribute}) as $item) {
                    $value[$item] = $item;
                }
                $this->owner->{$attribute} = $value;
            }
        }
    }
}   
