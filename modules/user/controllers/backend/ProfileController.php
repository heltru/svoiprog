<?php

namespace app\modules\user\controllers\backend;

use app\modules\user\forms\frontend\PasswordChangeForm;
use app\modules\user\forms\frontend\ProfileUpdateForm;
use app\modules\user\models\User;
use app\modules\user\Module;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class ProfileController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'model' => $this->findModel(),
        ]);
    }

    public function actionUpdate()
    {
        $model = $this->findModel();
        $model->scenario = \app\modules\user\models\backend\User::SCENARIO_CLIENT_UPDATE;
      //  $model = new ProfileUpdateForm($user);
        $model->load(Yii::$app->request->post());


        if ($model->load(Yii::$app->request->post()) && $model->update()) {

            /*
            if ($model->newPassword ) {
                // $model->setPassword( $model->newPassword );
                $model->password_hash = Yii::$app->security->generatePasswordHash($model->newPassword);
            }
            */
            return $this->redirect(['update']);
            //return $this->redirect(['/cabinet/default/view']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionPasswordChange()
    {
        $user = $this->findModel();
        $model = new PasswordChangeForm($user);

        if ($model->load(Yii::$app->request->post()) && $model->changePassword()) {
            Yii::$app->getSession()->setFlash('success', Module::t('module', 'FLASH_PASSWORD_CHANGE_SUCCESS'));
            return $this->redirect(['index']);
        } else {
            return $this->render('passwordChange', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @return User the loaded model
     */
    private function findModel()
    {
        return \app\modules\user\models\backend\User::findOne(Yii::$app->user->identity->getId());
    }
}
