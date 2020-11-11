<?php

namespace app\modules\app;
use app\modules\alco\models\Device;
use app\modules\alco\models\Persona;
use app\modules\smeta\models\Company;
use yii\helpers\ArrayHelper;

/**
 * app module definition class
 */
class AppModule extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\app\controllers';

    /**
     * @inheritdoc
     */
    private $company=null;
    private $balance=0;

    public function init()
    {
        parent::init();

        $this->company = Company::findOne(['user_id'=>\Yii::$app->user->id]);
        $this->balance = $this->company->balance;

        // custom initialization code goes here
    }

    public function getCompanyName(){
        if ($this->company){
            return $this->company->company_name;
        }
        return 'no name';
    }

    public function getBalance(){
        return $this->balance;
    }

    public function getCompanyId(){
        if ($this->company){
            return $this->company->id;
        }
        return 0;
    }

    public function getCompany(){
        return $this->company;
    }

    public function getDeviceIds(){
         return ArrayHelper::getColumn(Device::findAll(['persona_id'=>$this->company->id]) ,'id');
    }
}
