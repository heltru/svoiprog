<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;

use app\modules\main\assets\one\AssetLanding;
use app\modules\main\assets\one\AssetLandingEnd;

AssetLanding::register($this);
AssetLandingEnd::register($this);

?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>" data-test="434" prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# product: http://ogp.me/ns/product#" >
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
        <meta name="format-detection" content="telephone=no">
        <meta http-equiv="x-rim-auto-match" content="none">
        <?= Html::csrfMetaTags() ?>
        <?= ( $this->title ) ? '<title>'.Html::encode($this->title).'</title>' : '' ?>
        <!--[if lt IE 9]> <script src="scripts/html5.js"></script> <![endif]-->
        <?php $this->head() ?>
        <?php echo $this->render('//gtm/inhead') ?>
    </head>
    <body>
    <?php echo $this->render('//gtm/openbody') ?>
    <?php $this->beginBody() ?><?= $content ?>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>