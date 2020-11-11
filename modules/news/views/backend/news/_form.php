<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $form yii\widgets\ActiveForm */

?>

<div data-role="News">

    <?php if (!$model->isNewRecord && $dpDescr) { ?>
        <div class="row">
            <div class="col-xs-12">
                <h4>Блоки</h4>
                <?php

                Pjax::begin([
                    'id' => 'grid-ajax',
                ]);

                echo $this->render('include/grid', ['dpDescr' => $dpDescr]);
                Pjax::end();
                ?>
            </div>

            <div class="col-xs-12">
                <a style="margin-bottom: 1em"
                   href="<?= \yii\helpers\Url::to(['news-descr-add', 'id' => $model->id]) ?>"
                   class="btn btn-default">Ещё блок описаний</a>
            </div>
        </div>
    <?php } ?>

    <?php $form = ActiveForm::begin([
        'id' => 'news-form',
        'options' => [

            'enctype' => 'multipart/form-data'],
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['validate']),
    ]); ?>

    <?= $form->field($model, 'status')->hiddenInput(['maxlength' => true])->label(false) ?>
    <?= $form->field($model, 'first')->hiddenInput(['maxlength' => true])->label(false) ?>
    <?= $form->field($model, 'type')->hiddenInput(['maxlength' => true])->label(false) ?>
    <?= $form->field($model, 'date_public')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'news_cat_id')->dropDownList(\app\modules\news\service\NewsCat::itemsListTree()) ?>

    <?php
    if ($model->isNewRecord) {
        $model->date_public = time();
    }
    echo '<label>' . $model->getAttributeLabel('date_public') . '</label>';
    echo \kartik\date\DatePicker::widget([
        'name' => 'News[date_public]',
        'value' => date('d.m.Y', strtotime($model->date_public)),
        'pluginOptions' => ['todayHighlight' => true]
    ]);
    ?>


    <?php echo $this->render('@app/modules/url/views/backend/url/_form', ['url' => $url, 'form' => $form]); ?>

    <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>


    <?php ActiveForm::end(); ?>

</div>

