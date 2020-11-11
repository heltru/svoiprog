<?php

$this->title = 'Добавить редирект';
$this->params['breadcrumbs'][] = ['label' => 'Url', 'url' => ['/admin/url']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-xs-12">

<form method="post" action="<?= \yii\helpers\Url::to(['url/create-redirect-url']) ?>">
    <div class="row">

        <input id="form-token" type="hidden" name="<?=Yii::$app->request->csrfParam?>"
               value="<?=Yii::$app->request->csrfToken?>"/>


        <div class="col-xs-12">
            <?= \yii\helpers\Html::textInput('oldUrl',$oldHref,['id'=>'oldUrl',
                'class'=>'form-control','placeholder'=>'pivovarenie/ingredienty-dlya-piva/pivovarenie/ingredienty-dlya-piva/solod-karamelnyj']);

            ?>
            <?= \yii\helpers\Html::label('старый url','oldUrl') ?>
        </div>

        <div class="col-xs-12">
            <?= \yii\helpers\Html::textInput('newUrl',$newHref,['id'=>'newUrl',
                'class'=>'form-control','placeholder'=>'pivovarenie/ingredienty-dlya-piva/solod-karamelnyj'])?>
            <?= \yii\helpers\Html::label('новый url','newUrl') ?>
        </div>






        <div class="col-xs-12">
            <?php echo \yii\helpers\Html::submitButton('OK',['class'=>'btn btn-primary']) ?>
        </div>
    </div>

</form>

</div>
