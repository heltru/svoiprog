<?php
use backend\assets\AppAsset;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

dmstr\web\AdminLteAsset::register($this);
app\assets\AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

</head>
<script src="js/jquery.maskedinput.js">

</script>
<body class="login-page">

<?php $this->beginBody() ?>
<section class="content">
    <?= \dmstr\widgets\Alert::widget() ?>
    <?= $content ?>
</section>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
