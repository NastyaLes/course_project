<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user".
 *
 * @property int $id_user
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property string $password
 * @property string $token
 * @property int|null $admin
 *
 * @property Orders[] $orders
 */

//Active Record обеспечивает объектно-ориентированный интерфейс для доступа и манипулирования данными, хранящимися в базах данных

class User extends ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    public static function findIdentity($id)
    {

    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }

    public function getId()
    {
        return $this->id_user;
    }

    public function getAuthKey()
    {

    }

    public function validateAuthKey($authKey)
    {
        
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'phone', 'password', 'token'], 'required'],
            [['admin'], 'boolean'],
            [['first_name', 'last_name'], 'string', 'max' => 40],
            [['phone'], 'string', 'max' => 15],
            [['password'], 'string', 'max' => 100],
            [['token'], 'string', 'max' => 100],
        ];
     }

    /**
     * {@inheritdoc}
     */    
    public function attributeLabels()
    {
        return [
             'id' => 'ID',
             'first_name' => 'First Name',
             'last_name' => 'Last Name',
             'phone' => 'Phone',
             'password' => 'Password',
             'token' => 'Token',
             'admin' => 'Admin',
        ];
    }
    
    public function fields() //настройка отображения полей
    {
        $fields = parent::fields();
        // удаляем небезопасные поля
        unset($fields['admin'], $fields['password'], $fields['token']);
        return $fields;
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getOrders()
    {
        return $this->hasMany(Order::class, ['user_id' => 'id_user']);
    }
}
