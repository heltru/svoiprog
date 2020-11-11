<?php

namespace app\modules\user;

use app\modules\user\models\User;
use Yii;

class Module extends \yii\base\Module
{

    public $user;
    /**
     * @var string
     */
    public $defaultRole = 'user';
    /**
     * @var int
     */
    public $emailConfirmTokenExpire = 259200; // 3 days
    /**
     * @var int
     */
    public $passwordResetTokenExpire = 3600;

    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('modules/user/' . $category, $message, $params, $language);
    }

    public function init()
    {
        parent::init();
        $this->user = User::findOne(['id'=>\Yii::$app->user->id]);
    }

    public function getUser(){
        return $this->user;
    }
}
