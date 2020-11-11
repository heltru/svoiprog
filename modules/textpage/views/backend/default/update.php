<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Textpage */

$this->title = 'Обновить: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Страницы сайта', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name,/* 'url' => ['view', 'id' => $model->id]*/];
$this->params['breadcrumbs'][] = 'Обновить';
?>
<div class="textpage-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php

    echo $this->render('_form', [
        'model' => $model,
        'url' => $url,
    ]) ?>

</div>
