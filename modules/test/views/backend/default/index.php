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

    <div class="row main-panel-row" >
        <div class="col-md-4">
            <p>
                <?= Html::a('<span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> SitemapNot 200', ['sitemap-not200'], ['class' => 'abc btn btn-default']) ?>
            </p>

        </div>

        <div class="col-md-4">
            <?php echo \onmotion\telegram\Telegram::widget(); ?>

        </div>
        <div class="col-md-4">
            <p>
                <?= Html::a('<span class="" aria-hidden="true"></span> Test pay', ['pay'], ['class' => 'abc btn btn-default']) ?>
            </p>

        </div>

    </div>




</div>
