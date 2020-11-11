<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\News */

$this->title = "Новость";
$this->params['breadcrumbs'][] = ['label' => 'Новости', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name ];
$this->params['breadcrumbs'][] = 'Обновить';
?>
<div class="news-update">

    <h1><?=   $model->name ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'dpDescr'=>$dpDescr,
        'dataProviderImgLinks'=>$dataProviderImgLinks,
        'url' => $url,
    ]) ?>

</div>
