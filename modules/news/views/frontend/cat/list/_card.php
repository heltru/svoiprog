<?php
use yii\helpers\Html;
use yii\helpers\Url;
use  app\modules\image\models\ImgLinks;
use  app\modules\image\models\Img;


$imgProd = Img::getImgMain(190, ImgLinks::T_Ns, $product->id);
?>

<div class="product-container <?=$listType?>">
    <div class="product">
        <a href="<?= Url::to(['/news/news/view', 'id' => $product->id]) ?>" class="img-container">


            <?php
            $source ='';
            if (is_object($imgProd)){
                $webp = $imgProd->img_r->getWebPItem();
                $source = (is_object($webp)) ? '<source srcset="' . '/' . $webp->name_image. '" type="image/webp">':'';
            }

            ?>

            <picture>
                <?=$source?>
                <img src="<?= (is_object($imgProd)) ? $imgProd->img_r->src : '/' . Img::NOIMG ?>"
                     alt="<?= (is_object($imgProd)) ? $imgProd->img_r->Alt : 'alt' ?>"
                     title="<?= (is_object($imgProd)) ? $imgProd->img_r->Title : 'title' ?>"/>
            </picture>

            <div class="product-name">
                <span><?= $product->name?></span>
            </div>
        </a>

    </div>

</div>