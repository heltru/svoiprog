<?php

namespace app\modules\settings;
use app\modules\settings\models\SettingsTmpl;

/**
 * settings module definition class
 */
class SettingsModule extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\settings\controllers';
    private $data;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();


        $dependency = new \yii\caching\DbDependency([
            'sql' => 'SELECT  MAX(update_at) FROM ' . SettingsTmpl::tableName()
        ]);

        $result = SettingsTmpl::getDb()->cache(function ($db) {
            return SettingsTmpl::find()
                ->asArray()
                ->all();
        }, 0, $dependency);

        $this->data = [];


        foreach ($result as $item){

            $this->data[ $item['name'] ] = $item['value'];
        }
        // custom initialization code goes here
    }



    public function getVar($name){

        if (isset($this->data[$name])) return $this->data[$name];
        return null;
    }

    public function newVar($name,$val){
        $model = new SettingsTmpl();
        $model->name = $name;
        $model->description = '';
        $model->value = $val;
        $model->save();

      //  var_dump(  $model->getErrors() );exit;

    }
    public function editVar($name,$val){
        $model = SettingsTmpl::findOne(['name'=>$name]);
        if ($model !== null){
            $model->value = $val;
            $model->update(false,['value']);
        } else {
            $this->newVar($name,$val);
        }
        return $val;


    }

}
