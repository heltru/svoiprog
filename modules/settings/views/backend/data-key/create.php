<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\settings\models\DataKey */

$this->title = 'Create Data Key';
$this->params['breadcrumbs'][] = ['label' => 'Data Keys', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="data-key-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
