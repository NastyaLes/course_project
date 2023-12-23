<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "order".
 *
 * @property int $id_order
 * @property int $user_id
 * @property int $tariff_id
 * @property int $travel_time_min
 * @property float $price
 * @property string|null $options
 * @property string $status_order
 *
 * @property Tariff $tariff
 * @property User $user
 */
class Order extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'tariff_id'], 'required'],
            [['user_id', 'tariff_id'], 'integer'],
            [['options'], 'string', 'max' => 100],
            [['status_order'], 'match', 'pattern'=>'/^(Принято в обработку)+$|^(Подтвержден)+$|^(Выполнен)+$|^(Отказано)+$/u', 'message'=>'Статус может быть только: Принято в обработку, Подтвержден, Выполнен, Отказано'],
            [['tariff_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tariff::class, 'targetAttribute' => ['tariff_id' => 'id_tariff']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id_user']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_order' => 'Id Order',
            'user_id' => 'User ID',
            'tariff_id' => 'Tariff ID',
            'travel_time_min' => 'Travel Time Min',
            'price' => 'Price',
            'options' => 'Options',
            'status_order' => 'Status Order',
        ];
    }

    /**
     * Gets query for [[Tariff]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTariffs()
    {
        return $this->hasOne(Tariff::class, ['id_tariff' => 'tariff_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasOne(User::class, ['id_user' => 'user_id']);
    }
}
