<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\textpage\models\Textpage;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model Textpage */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="textpage-form">

    <?php $form = ActiveForm::begin(['id'=>'text-page-form',
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['validate-textpage'])]); ?>

    <div class="col-xs-12" style="margin-bottom: 1em;">
        <div class="btn-group pull-right" role="group"  >
            <?php
            if ($model->isNewRecord){
                $model->status = Textpage::ST_OK;
            }

            ?>
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?=  Textpage::$arrTxtStatus[$model->status ] ?> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li style="margin: 0.5em;">
                    <?php
                    echo $form->field($model, 'status')->radioList ( Textpage::$arrTxtStatus,
                        ['onchange'=>'$(this).parent().parent().parent().parent().find("button:first").html( $(this).find( "input:checked" ).parent().text() + " <span class=\'caret\'></span>" )' ]);
                    ?>
                </li>
            </ul>
            <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Обновить', [
                'id'=>'submitBtnTop',
                'class' => $model->isNewRecord ? 'btn btn-success btnSubm' : 'btn btn-primary btnSubm'
            ]) ?>

        </div>

    </div>




    <div class="col-xs-12 col-md-12">
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>

    <?php // echo $this->render('/geo-var/_help_table') ?>

    <div class="col-xs-12">
        <?=  $form->field($model, 'text')->widget(wadeshuler\ckeditor\widgets\CKEditor::className(),[
            'clientOptions' =>
                [
                    'height'=>500,
                    'contentsCss' => '/styles/style.css',
                    'filebrowserImageUploadUrl'=> '/admin/upload-editor/load-img?type=textpages&id=' . $model->id
                ],

        ])
      /*  $form->field($model, 'text')->widget(\vova07\imperavi\Widget::className(), [
            'settings' => [
                'lang' => 'ru',
                'minHeight' => 500,
                'plugins' => [
                    'clips',
                    'fullscreen'
                ]
            ]
        ]);*/
 ?>
    </div>

    <div class="col-xs-12">
        <?php echo  $this->render( '@app/modules/url/views/backend/url/_form' , ['url'=>$url ,'form'=>$form  ] );?>
    </div>


    <div class="col-xs-12 col-md-12">
        <?php
        if  ( $model->isNewRecord) {
            $model->sitemap =  Textpage::SM_OK;
            echo $form->field($model, 'sitemap')->radioList (Textpage::$arrTxtSitemap  );
        } else {
            echo $form->field($model,'sitemap')->radioList( Textpage::$arrTxtSitemap,
                ['value'=> $model->sitemap]  );
        }

        ?>
    </div>

    <div class="col-xs-12 col-md-12">
        <?php
        if  ( $model->isNewRecord)
            $model->type_page =  Textpage::TP_Or;

        echo  $form->field($model, 'type_page')->
        dropDownList( Textpage::$arrTxtTypePage ,
            [ 'id'=>'selTypePage' ]) ?>
    </div>

    <div class="col-xs-12 col-md-12">
        <?php
        if  ( $model->isNewRecord)
            $model->module =  Textpage::M_Txp;

        echo  $form->field($model, 'module')->
        dropDownList( Textpage::$arrModules /*,
            [ 'id'=>'selTypePage' ]*/) ?>
    </div>





    <div class="col-xs-12 col-md-12">
        <div class="form-group pull-right">
            <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>

<script>

    $(document).ready(function () {

        $('#text-page-form').on('ajaxComplete',function (e,jqXHR,textStatus) {

            if (textStatus == 'success'){
                $.each(jqXHR.responseJSON ,function (i,v) {
                    var arr =  i.split('-');

                    $('#text-page-form').
                    yiiActiveForm('updateAttribute',
                        arr[0]+'-'+arr[2], [ v ]);
                    console.log(
                        arr[0]+'-'+arr[2], [ v ]
                    );
                    if ( arr[0] == 'url' ){
                        $('div#demo_metatag').collapse('show');
                        $('.nav-tabs').find('a[href="#tab1"]').trigger('click');
                    }
                });
            }
        });



        $('#btnGenerHref').click(function (e) {

            makeHref();
        });
        $('#btnGenerWrHref').click(function (e) {
            makeHrefW();
        });


        function makeHrefW(){

            var txt = $('#fieldHref').val();

            if (txt){
                $.ajax({
                    type:"POST",
                    url:"<?= Url::to(['/admin/url/url/make-href']) ?>",
                    data:{txt:txt,_csrfbe:yii.getCsrfToken()},
                    success:function (data) {
                        if (typeof data == 'object'){
                            if (data.message == 'error'){
                                $('#product-update-eav-form').yiiActiveForm('updateAttribute', 'url-href', ["Такой url уже есть"]);
                            }
                            if (data.status == 200){

                                $('#fieldHref').val(data.data);

                            }
                        }
                    }
                });
            }


        }

        function makeHref(){

            var txt = $('#textpage-name').val();

            if (txt){
                $.ajax({
                    type:"POST",
                    url:"<?= Url::to(['/admin/url/url/make-href']) ?>",
                    data:{txt:txt,_csrfbe:yii.getCsrfToken()},
                    success:function (data) {
                        if (typeof data == 'object'){
                            if (data.message == 'error'){
                                $('#text-page-form').yiiActiveForm('updateAttribute', 'url-href', ["Такой url уже есть"]);
                            }
                            if (data.status == 200){

                                $('#fieldHref').val(data.data);

                            }
                        }
                    }
                });
            }
        }

    });
</script>