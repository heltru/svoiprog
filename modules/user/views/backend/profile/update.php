<?php

use yii\bootstrap\ActiveForm;
use app\modules\user\Module;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \app\modules\user\forms\frontend\ProfileUpdateForm */

$this->title = 'Обновить профиль';
//$this->params['breadcrumbs'][] = ['label' => 'Обновить', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-profile-update">



    <div class="user-form">

        <?php $form = ActiveForm::begin(['id' => 'profile-update-form']); ?>

        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'def_template')->textInput(['maxlength' => true]) ?>



        <div class="form-group">
            <?= Html::submitButton(Module::t('module', 'BUTTON_SAVE'), ['class' => 'btn btn-primary', 'name' => 'update-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
