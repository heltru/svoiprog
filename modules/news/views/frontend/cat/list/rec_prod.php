<?php if ( ! count($rec_prod)) return ; ?>
<div id="recommended-products" class="products-block">
    <p class="products-block-title">Рекомендуемые товары:</p>
    <?php
    foreach ( $rec_prod as $product) {
        echo $this->render('_card_r',['product'=>$product]);
    }
    ?>

    <?php if (  count($rec_prod) > 2){ ?>
    <div class="button-container"><span class="black-button">Еще</span></div>
    <?php }?>
</div>


