<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */


?>

<div class="row" data-role="Url">

    <div class="col-xs-12">
        <div class="row">
            <div class="col-xs-3" id="preBlock" style="display: none">
                <?= Html::activeLabel($url, 'preHref') ?>

                <?= Html::textInput('preHrefText', $url->preHref, [
                    'class' => 'form-control', 'id' => 'preHrefText']) ?>

                <?= $form->field($url, 'preHref')->hiddenInput(['id' => 'preHref'])->label(false) ?>

            </div>
            <div class="col-xs-9">
                <?= $form->field($url, 'href')
                    ->textInput(['id' => 'fieldHref', 'maxlength' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <a class="btn btn-default btn-sm btnAutoUrl" id="btnGenerWrHref">транслит href</a>
                <a class="btn btn-default btn-sm btnAutoUrl" id="btnGenerHref">транслит href авт.</a>
                <p class="errHref" style="color: red;display: none">такой есть!</p>
                <a class="btn btn-default btn-sm btnAutoUrl" id="btnH1Copy">копировать с name</a>
            </div>
        </div>

    </div>

    <div class="col-xs-12">
        <?= $form->field($url, 'h1')->textInput(['maxlength' => true]) ?>
    </div>


    <div class="col-xs-12">
        <?= $form->field($url, 'title')->textInput(['maxlength' => true]) ?>
        <p style="color: #968686;"><span id="msgWidthPx"></span></p>
        <div id="sizeTitleBox" class="size-title-box" style=" position: absolute;
    visibility: hidden;
    height: auto;
    width: auto;
    white-space: nowrap; "></div>
    </div>
    <div class="col-xs-12">
        <?= $form->field($url, 'description_meta')->textarea(['rows' => 4]) ?>
        <p style="color: #968686;">Символов: <span id="countLettersDescr"></span></p>
    </div>

    <div class="col-xs-12">
        <?= $form->field($url, 'keywords')->textInput(['maxlength' => true]) ?>
    </div>

</div>
