<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Аккаунт подтвержден';


?>

<div class="login-box">
    <div class="login-logo">
        <a href="/"><b>novasex</b><br>Панель управления</a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">
            Аккаунт подтвержден!
            <a href='<?= Yii::$app->homeUrl ?>'></a>
        </p>


    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->
