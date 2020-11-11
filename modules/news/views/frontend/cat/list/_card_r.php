<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\ImgLinks;
use common\models\Img;
$imgSticker = null;
if (is_object( $product->badge_r )){
    $imgSticker = Img::getImgMainByHeight(75,ImgLinks::T_Bd,$product->badge_r->id );
}

$imgProd = Img::getImgMain(296,ImgLinks::T_Pt,$product->id );

/*var_dump($imgProd->img_r);
exit;*/
?>

<div class="product">
<a href="<?= Url::to(['/product/view','id'=>$product->id]) ?>">
    <div class="product-info" >
        <?php if (is_object($product->badge_r)&& is_object($imgSticker)){
            ?>
        <img class="sticker" src="<?=  (is_object($imgSticker)) ?  $imgSticker->img_r->src : '/' . '/' . Img::NOIMG ?>"
             alt="<?=  (is_object($imgSticker)) ?  $imgSticker->img_r->alt : 'alt'?>"
             title="<?=  (is_object($imgSticker)) ?  $imgSticker->img_r->title : 'title'?>" />
        <?php } ?>
        <img src="<?=  (is_object($imgProd)) ?  $imgProd->img_r->src : '/' . Img::NOIMG ?>"
             alt="<?=  (is_object($imgProd)) ?  $imgProd->img_r->Alt : 'alt'?>"
             title="<?=  (is_object($imgProd)) ?  $imgProd->img_r->Title : 'title'?>" />
        <p class="product-name"><?=  $product->name ?></p>
    </div>
</a>
    <div class="buy-block">
        <p class="product-price">
            <span class="sum"><?=
                Yii::$app->formatter->asDecimal( $product->curr_price ,0) ?></span> <span class="currency"> руб.</span>
            <?php if (  Yii::$app->user->can('admin')) {
                echo Html::a('Редактировать',
                    Yii::$app->urlManagerBackEnd->createUrl(['admin/product/update','id'=>$product->id]),[
                        'target'=>'_blank'
                    ]);
                echo '<br><span>Открыт с '.
                    \DateTime::createFromFormat('Y-m-d H:i:s', $product->url_r->last_mod)->format('d.m.Y')
                    .'</span>';
            } ?>
        </p>
        <div class="buy-buttons">
            <div class="basket-add" data-type="product" data-id="<?=  $product->id ?>">&nbsp;</div>
            <div class="button one-click" data-type="product" data-cost="<?=$product->curr_price?>" data-id="<?=$product->id_crm ?>">Купить в 1 клик</div>
        </div>
    </div>
</div>

