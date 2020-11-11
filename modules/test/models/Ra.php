<?php

namespace app\modules\test\models;

use Yii;

/**
 * This is the model class for table "ra".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property int $s_o
 * @property string $login
 * @property string $pass
 */
class Ra extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ra';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['s_o','uc','lp'], 'integer'],
            [['name', 'email', 'phone'], 'string', 'max' => 512],
            [['login', 'pass'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            's_o' => 'S O',
            'login' => 'Login',
            'pass' => 'Pass',
        ];
    }
}
