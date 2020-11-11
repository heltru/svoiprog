<?php

namespace app\modules\settings\models;

use Yii;

/**
 * This is the model class for table "settings".
 *
 * @property integer $id
 * @property string $name
 * @property string $value
 * @property string $description
 */
class SettingsTmpl extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'unique'],
            [['name'], 'string', 'max' => 64],
            [['value'], 'safe' ],
            [['description'], 'string', 'max' => 256],
            [['update_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название(сист.имя)',
            'value' => 'Значение',
            'description' => 'Описание',
        ];
    }

    public function beforeSave($insert)
    {
        $this->update_at = date( 'Y-m-d H:i:s', time() );

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }



}
