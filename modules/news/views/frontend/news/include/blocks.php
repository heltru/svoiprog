<?php

use app\modules\news\models\NewsBlock;
use yii\helpers\Html;

?>

<?php
foreach ($news_blocks as $block) {

    switch ($block->type_block) {
        case NewsBlock::TB_BANNER: ?>
            <div data-role="news_block" data-block_id="<?= $block->id ?>">
                <?php if ($block->name) { ?>
                    <p class="new-subtitle"><?= $block->name ?></p>
                <?php } ?>
                <div class="news-banner">
                    <?php $img = $block->getImgList(1);
                    if (isset($img[0])) {
                        $href = $img[0]->img_r->name_image;
                        if (is_object($img[0]->img_r) && is_object($img[0]->img_r->parent_r)) {
                            $href = $img[0]->img_r->parent_r->name_image;
                        }
                        ?>
                        <a data-fancybox="product-gallery"
                           href="<?= '/' . $href ?>"
                           title="<?= Html::encode($img[0]->img_r->title) ?>"
                        >

                            <?php
                            $webp = $img[0]->img_r->getWebPItem();
                            $source = (is_object($webp)) ? '<source srcset="' . '/' . $webp->name_image . '" type="image/webp">' : '';
                            ?>
                            <picture>
                                <?= $source ?>
                                <img src=" <?= '/' . $img[0]->img_r->name_image ?>"
                                     alt="<?= Html::encode($img[0]->img_r->alt) ?>"
                                     title="<?= Html::encode($img[0]->img_r->title) ?>"/>
                            </picture>

                        </a>

                        </a>
                    <?php } ?>
                    <?= $block->desc ?>
                </div>
            </div>
            <?php
            break;
        case NewsBlock::TB_INTRO: ?>
            <div data-role="news_block" data-block_id="<?= $block->id ?>">
                <?php if ($block->name) { ?>
                    <p class="new-subtitle"><?= $block->name ?></p>
                <?php } ?>
                <div class="news-main">
                    <?php $img = $block->getImgList(1);
                    if (isset($img[0])) {
                        $href = $img[0]->img_r->name_image;
                        if (is_object($img[0]->img_r) && is_object($img[0]->img_r->parent_r)) {
                            $href = $img[0]->img_r->parent_r->name_image;
                        }
                        ?>

                        <a data-fancybox="product-gallery"
                           href="<?= '/' . $href ?>"
                           title="<?= Html::encode($img[0]->img_r->title) ?>"
                        >
                            <?php
                            $webp = $img[0]->img_r->getWebPItem();
                            $source = (is_object($webp)) ? '<source srcset="' . '/' . $webp->name_image . '" type="image/webp">' : '';
                            ?>
                            <picture>
                                <?= $source ?>
                                <img src=" <?= '/' . $img[0]->img_r->name_image ?>"
                                     alt="<?= Html::encode($img[0]->img_r->alt) ?>"
                                     title="<?= Html::encode($img[0]->img_r->title) ?>"/>
                            </picture>
                        </a>
                    <?php } ?>
                    <?= $block->desc ?>
                </div>
            </div>
            <?php
            break;
        case NewsBlock::TB_INTRO_INVERT: ?>
            <div data-role="news_block" data-block_id="<?= $block->id ?>">
                <?php if ($block->name) { ?>
                    <p class="new-subtitle"><?= $block->name ?></p>
                <?php } ?>
                <div class="news-main-invert">
                    <?php $img = $block->getImgList(1);
                    if (isset($img[0])) {
                        $href = $img[0]->img_r->name_image;
                        if (is_object($img[0]->img_r) && is_object($img[0]->img_r->parent_r)) {
                            $href = $img[0]->img_r->parent_r->name_image;
                        }
                        ?>
                        <a data-fancybox="product-gallery"
                           href="<?= '/' . $href ?>"
                           title="<?= Html::encode($img[0]->img_r->title) ?>"
                        >

                            <?php
                            $webp = $img[0]->img_r->getWebPItem();
                            $source = (is_object($webp)) ? '<source srcset="' . '/' . $webp->name_image . '" type="image/webp">' : '';
                            ?>
                            <picture>
                                <?= $source ?>
                                <img
                                    <?= ($block->id == 33) ? 'id="width_40per"' : '' ?>
                                        src=" <?= '/' . $img[0]->img_r->name_image ?>"
                                        alt="<?= Html::encode($img[0]->img_r->alt) ?>"
                                        title="<?= Html::encode($img[0]->img_r->title) ?>"/>
                            </picture>
                        </a>
                    <?php } ?>
                    <?= $block->desc ?>
                </div>
            </div>
            <?php
            break;
        case NewsBlock::TB_TWO_IMG_TXT: ?>
            <div data-role="news_block" data-block_id="<?= $block->id ?>">
                <?php if ($block->name) { ?>
                    <p class="new-subtitle"><?= $block->name ?></p>
                <?php } ?>

                <div class="news-dop">
                    <?php $img = $block->getImgList(1);
                    if (isset($img[0])) {
                        $href = $img[0]->img_r->name_image;
                        if (is_object($img[0]->img_r) && is_object($img[0]->img_r->parent_r)) {
                            $href = $img[0]->img_r->parent_r->name_image;
                        }
                        ?>


                        <a data-fancybox="product-gallery"
                           href="<?= '/' . $href ?>"
                           title="<?= Html::encode($img[0]->img_r->title) ?>"
                        >
                            <?php
                            $webp = $img[0]->img_r->getWebPItem();
                            $source = (is_object($webp)) ? '<source srcset="' . '/' . $webp->name_image . '" type="image/webp">' : '';
                            ?>

                            <picture>
                                <?= $source ?>
                                <img src=" <?= '/' . $img[0]->img_r->name_image ?>"
                                     alt="<?= Html::encode($img[0]->img_r->alt) ?>"
                                     title="<?= Html::encode($img[0]->img_r->title) ?>"
                                     itemprop="image"
                                />
                            </picture>


                        </a>


                    <?php } ?>
                    <div class="text">
                        <?= $block->desc ?>
                    </div>
                </div>
            </div>
            <?php
            break;
        case NewsBlock::TB_TWO_TXT: ?>
            <div data-role="news_block" data-block_id="<?= $block->id ?>">
                <?php if ($block->name) { ?>
                    <p class="new-subtitle"><?= $block->name ?></p>
                <?php } ?>
                <?= $block->desc ?>
            </div>
            <?php
            break;
    }
}
?>


