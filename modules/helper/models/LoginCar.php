<?php

namespace app\modules\helper\models;

use Yii;

/**
 * This is the model class for table "login_car".
 *
 * @property int $id
 * @property int $car_id
 * @property string $date_cr
 */
class LoginCar extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'login_car';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['car_id', 'date_cr'], 'required'],
            [['car_id'], 'integer'],
            [['date_cr'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'car_id' => 'Car ID',
            'date_cr' => 'Date Cr',
        ];
    }
}
