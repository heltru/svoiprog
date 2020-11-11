<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Textpage */

$this->title = 'Добавить';
$this->params['breadcrumbs'][] = ['label' => 'Страницы сайта', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="textpage-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'url' => $url,
    ]) ?>

</div>
