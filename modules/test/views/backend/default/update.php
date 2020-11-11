<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/**
 * Created by PhpStorm.
 * User: heltru
 * Date: 03.10.2018
 * Time: 0:04
 */

$form = ActiveForm::begin();

foreach ($settings as $index => $setting) {
    echo $form->field($setting, "[$index]name")->label($setting->name);
}

ActiveForm::end();