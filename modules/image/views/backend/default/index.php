<?php
$this->title = 'Вотермарки';
$this->params['breadcrumbs'][] = ['label'=>'Картинки','url'=>['/admin/image/default/index']] ;
$this->params['breadcrumbs'][] = $this->title;


$this->registerJsFile('/js/jquery-ui.min.js',  ['position' => yii\web\View::POS_END]);
$this->registerJsFile('/js/jquery.ui.touch-punch.min.js',  ['position' => yii\web\View::POS_END]);
?>


<div class="col-xs-12">
    <p>
        <?= \yii\helpers\Html::a('Добавить всем ватермарки', ['/admin/product/default/add-all-wm'],
            ['class' => 'btn btn-default']) ?>
    </p>
    <p>
        <?= \yii\helpers\Html::a('Удлаить у всех ватермарки', ['/admin/product/default/remove-all-wm'],
            ['class' => 'btn btn-default']) ?>
    </p>
</div>


<div class="col-xs-12">
    <?php

    $form = \yii\widgets\ActiveForm::begin([
        'id' => 'img-watermark-form',
        'options' => [
            'class' => 'form-horizontal','enctype'=>'multipart/form-data'
        ],
        'enableAjaxValidation' => true,

        'enableClientValidation' => true,
        'validateOnChange' => true,
        'validateOnSubmit'     => true,
    ]) ?>

    <div class="row">

        <div class="col-xs-12">
            <div class="form-group pull-right">
                <?= \yii\helpers\Html::submitButton( 'Обновить', [
                    'id'=>'submitBtnTop',
                    'class' =>  'btn btn-primary btnSubm'
                ]) ?>
            </div>
        </div>
    </div>

<div class="row images-form">
    <div class="col-xs-12" >
        <?php
        $tab_contentHeader = [
            '2545_374'=> '2545_374 Ватермарк (текст)',
            '2530_1743'=> '2530_1743 Ватермарк (текст + лого)',


        ]; ?>
        <script type='text/javascript'>
            <?php
            $php_array = array_keys($tab_contentHeader);
            $js_array = json_encode($php_array);
            echo "var matrSize = ". $js_array . ";\n";
            ?>
        </script>
        <?php
        echo  $this->render('@app/modules/image/views/image/img_form',['dataProviderImgLinks' =>
            $dataProviderImgLinks,'tab_contentHeader'=>$tab_contentHeader]);

        ?>
    </div>
</div>
    <?php \yii\widgets\ActiveForm::end() ?>

</div>
