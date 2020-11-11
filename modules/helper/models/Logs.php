<?php

namespace app\modules\helper\models;

use Yii;

/**
 * This is the model class for table "logs".
 *
 * @property integer $id
 * @property string $date
 * @property string $val
 * @property string $key
 */
class Logs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'logs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date'], 'safe'],
            [['val'], 'string' ],
            [['key'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Date',
            'val' => 'Val',
            'key' => 'Key',
        ];
    }

    public static  function log($key='',$data){
        $r = new Logs();
        $r->key = $key;
        $r->val = print_r($data,true); /* json_encode($data); *///var_export($data,true);/*json_encode($data);*/ //var_export($data,true);
        $r->date =   date('Y-m-d H:i:s');
        if (! $r->save()) {
            ex($r->getErrors());
        }

    }
}
