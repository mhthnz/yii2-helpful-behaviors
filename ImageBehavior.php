<?php
namespace app\behaviors;
use Yii;
use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\web\UploadedFile;

/**
 *  Behavior for uploading image
 *  @author Mhthnz
 *  @version 1.0.0
 */
class ImageBehavior extends Behavior
{
    /**
     *  @var image instance for upload
     */
    private $_image;

    /**
     *  @var old name of image
     */
    private $_oldImage;

    /**
     *  @var Owner attribute to image upload
     */
    private $_imageAttribute = 'image';

    /**
     *  @var Set upload dir
     */
    private $_uploadPath = '@webroot/uploads';

    /**
     *  @var Set url to upload dir
     */
    private $_uploadUrl = '@web/uploads';


    /**
     *  List of events
     *  @return []
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_BEFORE_INSERT => 'settingImageFilename',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'settingImageFilename',
            ActiveRecord::EVENT_AFTER_INSERT => 'saveImage',
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveImage',
        ];
    }

    /**
     *  Set model attribute
     */
    public function setImageAttribute($value)
    {
        $this->_imageAttribute = $value;
    }

    /**
     *  Set upload attribute
     */
    public function setUploadUrl($value)
    {
        $this->_uploadUrl = $value;
    }

    /**
     *  Set upload dir
     */
    public function setUploadPath($value)
    {
        $this->_uploadPath = $value;
    }

    /**
     *  Save old image
     */
    public function afterFind($event)
    {
        $this->_oldImage = $this->owner->image;

    }

    /**
     *  Save image on server and remove old image
     */
    public function saveImage($event)
    {
        if ($this->_image instanceof UploadedFile) {
            $path = Yii::getAlias($this->_uploadPath);
            if ($this->_image->saveAs($path . '/' . $this->owner->image)) {
                Yii::trace('Save image to: ' . $path . '/' . $this->owner->image);
            }
            if (is_file($path . '/' . $this->_oldImage)) {
                unlink($path . '/' . $this->_oldImage);
                Yii::trace('Remove old image: ' . $path . '/' . $this->_oldImage);    
            }
        }
    }

    /**
     *  Save Uload instance and generate filename
     */
    public function settingImageFilename($event)
    {   
        if ($this->owner->image instanceof UploadedFile) {
            $this->_image = $this->owner->image;
            $this->owner->image = Yii::$app->getSecurity()->generateRandomString() . '.' . $this->_image->extension;
        } else {
            $this->owner->image = $this->_oldImage;
        }
    }

    /**
     *  Set upload instance
     */
    public function beforeValidate($event)
    {
        $this->owner->image = UploadedFile::getInstance($this->owner, $this->_imageAttribute);
    }

    /**
     *  Get url to current image
     *  @return string|null Image url
     */
    public function getImageUrl()
    {
        if ($this->owner->image === null) {
            return null;
        }
        return Yii::getAlias($this->_uploadUrl . '/' . $this->owner->image);
    }

    /**
     *  Get path to current image
     *  @return string|null Image path
     */
    public function getImagePath()
    {
        if ($this->owner->image === null) {
            return null;
        }        
        return Yii::getAlias($this->_uploadPath . '/' . $this->owner->image);
    }
}