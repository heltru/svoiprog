<?php

namespace app\modules\settings\models;

use Yii;

/**
 * This is the model class for table "data_key".
 *
 * @property int $id
 * @property string $key
 * @property string $data
 */
class DataKey extends \yii\db\ActiveRecord
{

    const T_NAME_M = 'name_M';

    public static  $txtTypeArr = [self::T_NAME_M=>'Мужские имена'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'data_key';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key'], 'string', 'max' => 45],
            [['data'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'data' => 'Data',
        ];
    }
}
