<?php
use yii\helpers\Url;

$session = Yii::$app->session;
$session->open();
$userOrder = $session['userorder'];


$sortPrice = ''; $sortPop = '';
if (  is_array($userOrder) /* && $userField */ ){
    $userField = key($session['userorder']);
    if ( $userField == 'curr_price' && $userOrder[$userField] == SORT_ASC  ){
        $sortPrice = 'active-sort sort-up';
    }
    if ( $userField == 'curr_price' && $userOrder[$userField] == SORT_DESC  ){
        $sortPrice = 'active-sort';
    }
    if ( ($userField == 'count_popular' || $userField == 'ord_par_cat') && $userOrder[$userField] == SORT_ASC  ){
        $sortPop = 'active-sort sort-up';
    }
    if ( ($userField == 'count_popular' ||  $userField == 'ord_par_cat') && $userOrder[$userField] == SORT_DESC  ){
        $sortPop = 'active-sort';
    }
}
$session->close();
?>
<div id="category-products">
    <div id="sort-bar" data-url="<?= Url::to(['/catalog/order-filter']) ?>" data-page="<?= Yii::$app->request->get('page')?>">
        <p id="sort-title">Сортировать по:</p>
        <p id="<?=$userField?>" class="sort-option <?= $sortPop ?>">Популярности</p>
        <p id="sort-price" class="sort-option <?= $sortPrice ?>">Цене</p>
    </div>

    <div id="filter-results" class="products-block">
        <?php
        foreach ( $prod_row->getModels() as $product) {
            if (is_object($product))
               echo $this->render('_card',['product'=>$product]);
        }
        ?>

    </div>

    <div id="results-nav">
        <?php   if ( ! ($prod_row->pagination->page >= $prod_row->pagination->pageCount - 1)) {
            $page = $prod_row->pagination->page + 1;
            ?>
            <div class="button-link"
                 onclick="window.location.href='<?= $prod_row->pagination->createUrl($page) ?>'" >
                Показать ещё
            </div>
        <?php
        } ?>

        <?php

        echo  \justinvoelker\separatedpager\LinkPager::widget([
            'pagination' => $prod_row->pagination,
            'options'=> [
                'id'=>'nav-tabs',
                'class' => false
            ],
            'maxButtonCount'=>6,
            'nextPageLabel' => false,
            'prevPageLabel' => false,
            'activePageCssClass' =>'active-tab'
        ]);
        ?>

    </div>

</div>



