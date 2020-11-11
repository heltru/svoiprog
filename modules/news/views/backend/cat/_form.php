<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\news\models\NewsCat */
/* @var $form yii\widgets\ActiveForm */
$cats = \yii\helpers\ArrayHelper::map(
    \app\modules\news\models\NewsCat::find()->all(),'id','name'
);
?>

<div class="news-cat-form" data-role="Cat">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'parent_id')->dropDownList($cats,['prompt'=>'---']) ?>

    <?= $form->field($model,'seo_menu_item')->dropDownList($cats,['prompt'=>'---']); ?>

    <?php echo $this->render('@app/modules/url/views/backend/url/_form', ['url' => $url, 'form' => $form]); ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
