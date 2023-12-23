<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * This is the model class for table "tariff".
 *
 * @property int $id_tariff
 * @property string $name_tariff
 * @property string|null $photo
 * @property float $price_min
 * @property int $category_id
 * @property string $timestamp
 *
 * @property Category $category
 * @property Order[] $orders
 */
class Tariff extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tariff';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_tariff', 'price_min', 'category_id'], 'required'],
            [['price_min'], 'number'],
            [['category_id'], 'integer'],
            [['name_tariff'], 'string', 'max' => 40],
            [['photo'], 'file', 'extensions' => ['png', 'jpg', 'gif', 'jpeg'], 'maxSize' => 1024*1024*2],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id_category']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_tariff' => 'Id Tariff',
            'name_tariff' => 'Name Tariff',
            'photo' => 'Photo',
            'price_min' => 'Price Min',
            'category_id' => 'Category ID',
            'timestamp' => 'Timestamp',
        ];
    }

    public function beforeValidate()
    {
        $this->photo=UploadedFile::getInstanceByName('photo');
        return parent::beforeValidate();
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id_category' => 'category_id']);
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::class, ['tariff_id' => 'id_tariff']);
    }
}
