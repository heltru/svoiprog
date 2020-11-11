<?php

use app\modules\admin\Module;
use app\modules\user\Module as UserModule;
use app\modules\url\UrlModule as UrlModule;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \app\modules\user\models\backend\User */

$this->title = Module::t('module', 'ADMIN');
?>
<div class="admin-default-index ">
    <h1><?= Html::encode($this->title) ?></h1>
<br>
    <div class="row main-panel-row">

        <div class="col-md-4">
            <p>
                <?= Html::a(
                    '<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> Статьи',
                    ['news/news/index'],
                    ['class' => 'abc btn btn-default']) ?>
            </p>
            <p>
                <?= Html::a(
                    '<span class="glyphicon glyphicon-th-large" aria-hidden="true"></span> Категории статей',
                    ['news/cat/index'],
                    ['class' => 'abc btn btn-default']) ?>
            </p>
        </div>


        <div class="col-md-4">
            <p>
                <?= Html::a('<span class="glyphicon glyphicon-usd" aria-hidden="true"></span>Urls', ['url/default/index'], ['class' => 'abc btn btn-default']) ?>
            </p>

        </div>


        <div class="col-md-4">
            <p>
                <?= Html::a('<span class="glyphicon glyphicon-star" aria-hidden="true"></span>
 Страницы сайта', ['textpage/default/index'], ['class' => 'abc btn btn-default']) ?>
            </p>

        </div>

    </div>




</div>
