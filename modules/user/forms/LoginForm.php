<?php

namespace app\modules\user\forms;

use app\modules\helper\HelperModule;
use app\modules\user\models\User;
use app\modules\user\Module;
use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model
{
    public $phone;
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Module::t('module', 'USER_USERNAME'),
            'password' => Module::t('module', 'USER_PASSWORD'),
            'rememberMe' => Module::t('module', 'USER_REMEMBER_ME'),
            'phone'=>'Номер телефона'
        ];
    }



    function cleanStr($value){
        $value = str_replace('Â', '', $value);
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        $value = trim($value);
        // $value = str_replace(chr(194)," ",$value);

        return $value;
    }

    /**
     * Validates the username and password.
     * This method serves as the inline validation for password.
     */

    public function validatePassword()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            $this->password = $this->cleanStr($this->password);

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError('password', Module::t('module', 'ERROR_WRONG_USERNAME_OR_PASSWORD'));
            } elseif ($user && $user->status == User::STATUS_BLOCKED) {
                $this->addError('username', Module::t('module', 'ERROR_PROFILE_BLOCKED'));
            } elseif ($user && $user->status == User::STATUS_WAIT) {
                
                $this->addError('username', Module::t('module', 'ERROR_PROFILE_NOT_CONFIRMED'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        $this->validate();

        if ($this->validate()) {

            return  Yii::$app->user->login($this->getUser(),$this->rememberMe ? 3600*24*30 : 0);
        } else {

            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        /*
        if ($this->_user === false) {
            $this->_user = User::findByPhone( HelperModule::formatPhoneDB($this->phone));
            ex($this->_user );
        }
        */
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);

        }



        return $this->_user;
    }

}
