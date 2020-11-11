<?php

namespace app\modules\user\controllers\frontend;

use app\modules\app\app\AddNewUser;
use app\modules\helper\models\Logs;
use app\modules\user\forms\frontend\EmailConfirmForm;
use app\modules\user\forms\LoginForm;
use app\modules\user\forms\frontend\PasswordResetForm;
use app\modules\user\forms\frontend\PasswordResetRequestForm;
use app\modules\user\forms\frontend\SignupForm;
use app\modules\user\Module;
use Yii;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class DefaultController extends Controller
{
    /**
     * @var \app\modules\user\Module
     */
    public $module;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->redirect(['profile/index'], 301);
    }

    public function actionLogin()
    {

        $this->layout = '/adminlte/main-login';
        if (!Yii::$app->user->isGuest) {
            return $this->redirect('admin');
            //return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect('admin');
          //  return $this->goBack();
        } else {
            return $this->render('adminlte/login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionSignup()
    {
        $this->layout = '/adminlte/main-login';
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $app_create_user = new AddNewUser();


            if ($app_create_user->addNewUser($model)) {

                $user = $app_create_user->getUser();




                mail($model->email,
                    'Email confirmation for ' . Yii::$app->name,
                    Yii::$app->getView()->renderFile('@app/modules/user/mails/emailConfirm.php',['user' => $user])
                );

                /* Yii::$app->mailer->compose(['text' => '@app/modules/user/mails/emailConfirm'], ['user' => $user])
               ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
               ->setTo($this->email)
               ->setSubject('Email confirmation for ' . Yii::$app->name)
               ->send();*/


                Yii::$app->getSession()->setFlash('success', 'Добро пожаловать в Mirovid!');
                return $this->redirect('admin');
            } else {
                $estr = '';
                foreach ($model->getErrors() as $att_name =>$errs){
                    $estr .= $model->getAttributeLabel($att_name) . ': ' . join(', ',$errs);
                }

                Yii::$app->getSession()->setFlash('danger', 'Ошибка создания аккаунта ' . $estr . ' ' .$app_create_user->error);
            }
        }

        return $this->render('adminlte/signup', [
            'model' => $model,
        ]);
    }



    public function actionEmailConfirm($token)
    {
        $this->layout = '/adminlte/main-login';
        try {
            $model = new EmailConfirmForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }


        if ($model->confirmEmail()) {
            Yii::$app->getSession()->setFlash('success', Module::t('module', 'FLASH_EMAIL_CONFIRM_SUCCESS'));
            return $this->redirect('/admin');
            //return $this->render('adminlte/success-econfirm');
        } else {
            Yii::$app->getSession()->setFlash('error', Module::t('module', 'FLASH_EMAIL_CONFIRM_ERROR'));
        }

        return $this->goHome();
    }

    public function actionPasswordResetRequest()
    {
        $model = new PasswordResetRequestForm($this->module->passwordResetTokenExpire);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', Module::t('module', 'FLASH_PASSWORD_RESET_REQUEST'));

                return $this->goHome();
            } else {
                Yii::$app->getSession()->setFlash('error', Module::t('module', 'FLASH_PASSWORD_RESET_ERROR'));
            }
        }

        return $this->render('passwordResetRequest', [
            'model' => $model,
        ]);
    }

    public function actionPasswordReset($token)
    {
        try {
            $model = new PasswordResetForm($token, $this->module->passwordResetTokenExpire);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', Module::t('module', 'FLASH_PASSWORD_RESET_SUCCESS'));

            return $this->goHome();
        }

        return $this->render('passwordReset', [
            'model' => $model,
        ]);
    }
}
