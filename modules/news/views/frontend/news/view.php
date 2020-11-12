<?php

use app\modules\news\models\News;

$bc_cat = $news->newsCat_r->name;
$bc_cat_url = \yii\helpers\Url::to(['/news/cat/view', 'id' => $news->news_cat_id]);

?>
<style>
    div[data-role="news_block"]:hover {
        border: 1px solid #cecbcb;
    }
</style>
<main id="content" class="info-page">
    <div class="content-container">
        <ul class="breadcrumb">
            <li typeof="v:Breadcrumb"><a href="/" rel="v:url"
                                         property="v:title"><?= Yii::$app->params['siteName'] ?></a></li>
            <?php if ($news->type == News::T_OWN) { ?>
                <li typeof="v:Breadcrumb"><a href="<?= $bc_cat_url ?>" rel="v:url" property="v:title"><?= $bc_cat ?></a>
                </li>
            <?php } ?>
            <li><span><?= $news->name ?></span></li>
        </ul>

        <h1><?= $url->h1 ?></h1>

        <div class="info-block news" data-role="NewsBlocksEditor">
            <p id="date"><?= \DateTime::createFromFormat('Y-m-d H:i:s', $news->date_public)->format('d.m.Y') ?></p>
            <?= $this->render('include/blocks', ['news_blocks' => $news_blocks]); ?>
        </div><!-- end of info-block -->


    </div><!-- end of content-container -->

</main><!-- end of content -->
<?php if (\app\modules\helper\models\Helper::getIsAdmin(Yii::$app->user->id) ){ ?>
<!-- WindowsModal NewsBlockEdit -->
<div id="wndNewsBlockEdit" style="display: none">
    <form>
        <div data-role="content">
        </div>
        <div data-role="btn_save" class="button"  style="width: 200px;
    margin: 0 auto;
    margin-top: 15px;">Сейв
        </div>
    </form>

</div>
<?php } ?>
