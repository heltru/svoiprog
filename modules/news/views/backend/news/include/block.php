<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\modules\news\models\NewsBlock;

$this->registerJsFile('/js/jquery-ui.min.js', ['position' => yii\web\View::POS_END]);
$this->registerJsFile('/js/jquery.ui.touch-punch.min.js', ['position' => yii\web\View::POS_END]);

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */

$this->title = ($model->isNewRecord) ? 'Добавить блок описаний' : 'Обновить блок описаний';
$this->params['breadcrumbs'][] = ['label' => 'Новости', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->news_r->name,
    'url' => \yii\helpers\Url::to(['update', 'id' => $model->news_id, '#' => 'tab12'])];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-xs-12">

    <div class="row">
        <?php $form = ActiveForm::begin([
                'id' => 'product-descrimg-form',
                'options' => ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data'
                ],
                'enableAjaxValidation' => true,
                'validationUrl' => Url::to(['news-descr-validate']),
            ]
        ); ?>

        <div class="col-xs-12">
            <div class="form-group pull-right">
                <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Обновить',
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>

        <div class="col-xs-12">

            <?= $form->field($model, 'type_block')->
            dropDownList(NewsBlock::$arrTxtBlock, ['prompt' => 'Выберите тип блока']); ?>

            <p id="helperMsg_<?= NewsBlock::TB_INTRO ?>" class="helperMsgs hint-block"
               style="<?= (!$model->isNewRecord && $model->type_block == NewsBlock::TB_INTRO) ? '' : 'display: none' ?>">
                Текстовый блок + картинка справа ( 558x351 )
            </p>
            <p id="helperMsg_<?= NewsBlock::TB_INTRO_INVERT ?>" class="helperMsgs hint-block"
               style="<?= (!$model->isNewRecord && $model->type_block == NewsBlock::TB_INTRO_INVERT) ? '' : 'display: none' ?>">
                Картинка слева + Текстовый блок ( 558x351 )
            </p>
            <p id="helperMsg_<?= NewsBlock::TB_TWO_IMG_TXT ?>" class="helperMsgs hint-block"
               style="<?= (!$model->isNewRecord && $model->type_block == NewsBlock::TB_TWO_IMG_TXT) ? '' : 'display: none' ?>">
                Текстовый блок слева + картинка справа (150x120)
            </p>
            <p id="helperMsg_<?= NewsBlock::TB_TWO_TXT ?>" class="helperMsgs hint-block"
               style="<?= (!$model->isNewRecord && $model->type_block == NewsBlock::TB_TWO_TXT) ? '' : 'display: none' ?>">
                Текстовый блок слева + картинка справа (150x120)
            </p>
            <p id="helperMsg_<?= NewsBlock::TB_BANNER ?>" class="helperMsgs hint-block"
               style="<?= (!$model->isNewRecord && $model->type_block == NewsBlock::TB_BANNER) ? '' : 'display: none' ?>">
                Картинка большая (1165x776)
            </p>
        </div>


        <div class="col-xs-12">
            <?php

            echo $form->field($model, 'name')->textInput() ?>
        </div>

        <div class="col-xs-12">
            <?= $form->field($model, 'desc')->widget(wadeshuler\ckeditor\widgets\CKEditor::className(), [
                'clientOptions' =>
                    [
                        'height' => 500,
                        //     'contentsCss' => '/styles/style.css',
                        'filebrowserImageUploadUrl' => '/admin/upload-editor/load-img?type=products&id=' . $model->news_id
                    ],
            ])->hint('Разделитель блоков текста символ @')->label($model->getAttributeLabel('desc'),
                ['class' => 'control-label modalSaveVarValue']) ?>
        </div>


        <div class="col-xs-12">
            <?php
            $tab_contentHeader = [

                '558_351' => '558_351 Заглавный',
                '150_120' => '150_120 Второстепенный',
                '1165_776' => '1165_776 Второстепенный',
                '1160_503' => '1160_503 Прямоугольный',
                '914_856' => '914_856 Квадратный',
                '0_0' => 'Авто',

            ]; ?>
            <script type='text/javascript'>
                <?php
                $php_array = array_keys($tab_contentHeader);
                $js_array = json_encode($php_array);
                echo "var matrSize = " . $js_array . ";\n";
                ?>
            </script>
            <?php
            echo $this->render('@app/modules/image/views/image/img_form', ['dataProviderImgLinks' =>
                $dataProviderImgLinks, 'tab_contentHeader' => $tab_contentHeader]);
            ?>
        </div>

        <?= Html::activeHiddenInput($model, 'news_id') ?>

        <div class="col-xs-12">
            <div class="form-group pull-right">
                <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Обновить',
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
<script>
    $(document).ready(function () {


        $('#newsblock-type_block').change();

        $('#newsblock-type_block').change(function () {

            var type = String($(this).val());

            $('.helperMsgs').hide();
            $('#helperMsg_' + type).show();
            /*  if ( type == type_order ){
                  $('.itemTitle').show();
              } else {
                  $('.itemTitle').hide();
              }*/
        });

    });
</script>






