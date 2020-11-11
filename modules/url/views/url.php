<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $url common\models\Url */
/* @var $form yii\widgets\ActiveForm */

//$url = new \common\models\Url();
?>

<div class="row">
    <div class="col-xs-12 create-rel-form">
        <button id="btnShowForm"  style="" type="button" class="btn btn-default btn-xs dropdown-toggle form-control"
                data-toggle="collapse" data-target="#demo_metatag">
            Мета-теги карточки
            <span class="caret"></span>
        </button>
        <div id="demo_metatag"  class="url-form collapse">
            <?php /* $form = ActiveForm::begin([
                    'id'=>'url-form',
                 'enableAjaxValidation' => true,
                   'validationUrl' => \yii\helpers\Url::to(['/url/validate-url']),
            ]);*/?>
            <div class="row">
                <div class="col-xs-12">
                    <div class="row">
                        <div class="col-xs-3" id="preBlock" style="display: none">
                            <?= Html::activeLabel($url,'preHref')?>

                            <?= Html::textInput('preHrefText',$url->preHref ,[
                                'class'=>'form-control','id'=>'preHrefText',
                                /*'disabled' => true*/ ]) ?>

                            <?= $form->field($url, 'preHref')->hiddenInput([  'id' => 'preHref' /* ,
                              'disabled' => true*/ ])->label(false) ?>
                        </div>
                        <div class="col-xs-9">
                            <?= $form->field($url, 'href' /*, ['enableAjaxValidation' => true]*/ )
                                ->textInput([  'id' => 'fieldHref' ,'maxlength' => true]) ?>
                        </div>
                    </div>
                    <a class="btn btn-default btn-sm btnAutoUrl" id="btnGenerWrHref">транслит href</a>
                    <a class="btn btn-default btn-sm btnAutoUrl" id="btnGenerHref">транслит href авт.</a>
                    <p class="errHref" style="color: red;display: none">такой есть!</p>
                </div>
                <div class="col-xs-12">
                    <?= $form->field($url, 'title')->textInput(['maxlength' => true]) ?>
                    <p  style="color: #968686;"><span id="msgWidthPx"></span></p>
                    <div id="sizeTitleBox" class="size-title-box" ></div>
                </div>
                <div class="col-xs-12">
                    <?= $form->field($url, 'description_meta')->textarea(['rows' => 6]) ?>
                    <p style="color: #968686;">Символов: <span id="countLettersDescr"></span></p>
                </div>
                <div class="col-xs-12">
                    <?= $form->field($url, 'h1')->textarea(['rows' => 6]) ?>
                </div>
                <div class="col-xs-12">
                    <?= $form->field($url, 'keywords')->textarea(['rows' => 4]) ?>
                </div>

                <div class="col-xs-12">
                    <button id="btnShowSubForm"   type="button" class="btn btn-default btn-xs dropdown-toggle form-control"
                            data-toggle="collapse" data-target="#subform_metatag">
                        прочее
                        <span class="caret"></span>
                    </button>
                </div>

                <div id="subform_metatag"  class="url-form collapse col-xs-12">
                    <div class="row">

                        <div class="col-xs-12">

                            <?php
                            echo  $form->field($url, 'real_canonical')->textInput(['maxlength' => true])
                                ->hint('если пусто, то берется url значение'); ?>
                        </div>

                        <!--  <div class="col-xs-12">
                            <?php //echo $form->field($url, 'redirect')->textInput(['maxlength' => true]) ?>
                        </div> -->

                        <div class="col-xs-12">
                            <?= $form->field($url, 'domain_id')->textInput(['maxlength' => true]) ?>

                            <?= Html::hiddenInput('Url[old_href]',$url->old_href,['id'=>'old_href']) ?>

                            <?php
                            if ($url->isNewRecord){
                                $url->last_mod = time();  Yii::$app->formatter->asDate( time() , "d/m/Y");
                            }
                            echo $form->field($url, 'last_mod')->widget(\yii\jui\DatePicker::classname(), [
                                'language' => 'ru',
                                'dateFormat' => 'php:d-m-Y',
                                'options'=>[
                                    'class'=>'form-control datepicker',

                                ]

                            ]) ;
                            ?>
                            <?= $form->field($url, 'pagination')->checkbox()?>


                        </div>

                    </div>
                </div>
            </div>

            <?php //ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {

        $('#url-description_meta').keyup(function (e) {
            $('#countLettersDescr').html($(this).val().length);
        });
        $('#url-description_meta').change(function (e) {
            $('#countLettersDescr').html($(this).val().length);
        });

        $('#url-title').change(function (e) {
            $('#sizeTitleBox').html($(this).val());
        });

        $('#url-title').keyup(function (e) {
            $('#sizeTitleBox').html($(this).val());
            calcWidth();
        });

        $('#url-title').change(function (e) {
            $('#sizeTitleBox').html($(this).val());
            calcWidth();
        });

        function calcWidth() {
            var test = document.getElementById("sizeTitleBox");
            test.style.fontSize = '18px';
            test.style.fontWeight = '400';
            test.style.lineHeight = '22px';
            test.style.fontFamily = 'Arial, Helvetica, sans-serif;';
            var width = (test.clientWidth + 1 ) + "px";
            $('#msgWidthPx').html(width);
        }


        $('#fieldHref').bind('focusout',function (e) {


            var  url =  $(this).val() ;
            if ($("#preHrefText").val()) url =  $("#preHrefText").val() + "/"+ url;

            console.log($('#url-real_canonical').val() == url );

            if ( $('#url-real_canonical').val()
                && url != $('#url-real_canonical').val() ){
                alert('url и rel=canonical не совпадают');
                return true;
            }

        });

        $('#url-real_canonical').bind('focusout',function (e) {
            var  url =  $(this).val() ;

            if ($("#preHrefText").val()) url =  $("#preHrefText").val() + "/"+ url;

        console.log($('#url-real_canonical').val() == url );

            if (
                $('#url-real_canonical').val()
                && ( url != $('#url-real_canonical').val() )
            ){
                alert('url и rel=canonical не совпадают');
                return true;
            }
        });


    });
</script>