<?php

/* @var $this \yii\web\View */
/* @var $content string */
use yii\helpers\Html;



\app\modules\news\assets\AssetCatalog::register($this);
\app\modules\news\assets\AssetCatalogEnd::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# product: http://ogp.me/ns/product#">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
    <meta name="format-detection" content="telephone=no">
    <meta http-equiv="x-rim-auto-match" content="none">
    <?= Html::csrfMetaTags() ?>
    <?= ($this->title) ? '<title>' . Html::encode($this->title) . '</title>' : '' ?>
    <?php $this->head() ?>
    <!--[if lt IE 9]>
    <script src="scripts/html5.js"></script> <![endif]-->
    <?php echo $this->render('//gtm/inhead') ?>
    <?php //echo $this->render('push/_push') ?>
</head>
<body>
<?php  echo $this->render('//gtm/openbody') ?>
<?php $this->beginBody() ?>


<header id="header">
    <?= $this->render('//_header'); ?>
</header> <!-- end of header -->

<main id="content" class="category">
    <?= $content ?>
</main><!-- end of main -->



<?php $this->endBody() ?>


</body>
</html>
<?php $this->endPage() ?>
