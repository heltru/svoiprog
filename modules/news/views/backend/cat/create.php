<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\news\models\NewsCat */

$this->title = 'Create News Cat';
$this->params['breadcrumbs'][] = ['label' => 'News Cats', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-cat-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'url' => $url,
    ]) ?>

</div>
