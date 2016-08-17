# Helpful behaviors
Save behavior to `app\behaviors`. If you use advanced app, then save to `common\behaviors` and change namespace `namespace app\behaviors;`.

# Yii2-SetColumnBehavior
Behavior for working with MySQL column type `SET`. Behavior for automatical convert string => array and back.
# How to use:
In model we need to set model attributes which have type `SET`:
```php
/**
 *  @return [] behaviors
 */
public function behaviors()
{
   return [
       'SetColumn' => [
            'class' => app\behaviors\SetColumnBehavior::className(),
            'attributes' => 'weekdays',
        ]
    ];
}

public function rules()
{
    return [
       [['weekdays'], 'each', 'rule' => ['in', 'range' => ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat']]],
    ];
}
```
And render in view:
```php
echo $form->field($model, 'weekdays')->checkboxList(['sun' => 'sun', 'mon' => 'mon', 'tue' => 'tue', 'wed' => 'wed', 'thu' => 'thu', 'fri' => 'fri', 'sat' => 'sat']);
```

# Yii2-ImageBehavior
Easy way to attach image in your model. Automatically assign UploadFile instance to your attribute, stores and remove old image.
But validation rules you need to set.
# How to use:
Attach behavior to your model:
```php
    /**
     *  @return [] behaviors
     */
    public function behaviors()
    {
	return [
            'imageBehavior' => [
                'class' => \path\to\ImageBehavior::className(),
                'imageAttribute' => 'image',  //Attribute of model which using to upload image
                'uploadPath' => '@webroot/uploads',  //Path to upload dir
                'uploadUrl' => '@web/uploads',  //Url to upload dir	
            ],
        ];
    }
```
# Examples
In controller behavior absolutely imperceptible:
```php
$model = new Model(); // or Model::findOne()
if ($model->load(Yii::$app->request->post())) {
        if ($model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }    
}
```

As there helper methods:
```php
$model = Model::findOne();
echo $model->imageUrl;  //returned url of current image
echo $model->imagePath; //returned path of current image
```

# Yii2 - DateToTimestamp
Behavior for automatically change date to timestamp and back. It allows you to store the date in the timestamp format, and show in the desired format.
#How to use
Exist a few ways for attaching attributes:
```php
    /**
     *  @return [] behaviors
     */
    public function behaviors()
    {
	return [
            'DateToTimestampBehavior' => [
                'class' => \path\to\DateToTimestampBehavior::className(),
		'attributes' => 'delivery_date', // Attribute of model with date format
		'format' => 'M/d/Y', // Format for convert
            ],
        ];
    }
```
List of attributes:
```php
    /**
     *  @return [] behaviors
     */
    public function behaviors()
    {
	return [
            'DateToTimestampBehavior' => [
                'class' => \path\to\DateToTimestampBehavior::className(),
		'attributes' => [
			'delivery_date',
			'schedule_date',
			'register_date',
		],
		'format' => 'M/d/Y', // Format for convert
            ],
        ];
    }
```
Customize some attributes:
```php
    /**
     *  @return [] behaviors
     */
    public function behaviors()
    {
	return [
            'DateToTimestampBehavior' => [
                'class' => \path\to\DateToTimestampBehavior::className(),
		'attributes' => [
			'delivery_date',
			[
				'attribute' => 'schedule_date',
				'format' => 'Y.m.d H:i',
			],
			'register_date',
			[
				'attribute' => 'birth_date',
				'format' => 'd M',
			],
		],
		'format' => 'M/d/Y', // Format for convert
            ],
        ];
    }
```
