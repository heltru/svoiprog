<?php
use yii\helpers\Url;

$session = Yii::$app->session;
$session->open();
$userOrder = $session['userorder'];
$userView = $session['userview'];

$activeSort = ['news'=>'','pop'=>'','price'=>''];
//ex($userOrder);
if (  is_array($userOrder) /* && $userField */ ){
    $userField = key($session['userorder']);

    if ( $userField == 'product_modification.price_roz' && $userOrder[$userField] == SORT_ASC  ){
        $activeSort['price'] = 'active-sort sort-up';
    }
    if ( $userField == 'product_modification.price_roz' && $userOrder[$userField] == SORT_DESC  ){
        $activeSort['price'] = 'active-sort';
    }

    if ( ($userField == 'product.count_popular' || $userField == 'ord_par_cat') && $userOrder[$userField] == SORT_ASC  ){
        $activeSort['pop'] = 'active-sort sort-up';
    }
    if ( ($userField == 'product.count_popular' ||  $userField == 'ord_par_cat') && $userOrder[$userField] == SORT_DESC  ){
        $activeSort['pop'] = 'active-sort';
    }

    if ( ($userField == 'date_create' ) && $userOrder[$userField] == SORT_ASC ){
        $activeSort['news'] = 'active-sort sort-up';
    }
    if ( ($userField == 'date_create') && $userOrder[$userField] == SORT_DESC  ){
        $activeSort['news'] = 'active-sort';
    }

    if ( ($userField == 'badge_id' ) && $userOrder[$userField] == SORT_ASC ){
        $activeSort['news'] = 'active-sort sort-up';
    }
    if ( ($userField == 'badge_id') && $userOrder[$userField] == SORT_DESC  ){
        $activeSort['news'] = 'active-sort';
    }

}


$viewType = ['list'=>'','tile'=>'active-view'];
$listType = '';

if ( isset($userView) && $userView != 'view-tile' ){
    $viewType['tile'] = '';
    $viewType['list'] = 'active-view';

    $listType = 'list';
}

$session->close();
?>

<div class="controls simple-columns">
    <div id="sort"
         data-url_id="<?= (is_object($url)) ? $url->id : '' ?>"
         data-url="<?= Url::to(['/catalog/order-filter']) ?>" data-page="<?= Yii::$app->request->get('page')?>">
        <p id="sort-title">Сортивать по:</p>
        <p id="sort-new" class="sort-option <?= $activeSort['news'] ?>">Новизне</p>
        <p id="sort-pop" class="sort-option <?= $activeSort['pop'] ?>">Популярности</p>
        <p id="sort-price" class="sort-option <?= $activeSort['price'] ?>">Цене</p>
    </div>

    <div id="view" data-url="<?php //echo  Url::to(['/catalog/save-user-view']) ?>">
        <!--
        <p id="view-list" class="view-option <?php //$viewType['list'] ?>">&nbsp;</p>
        <p id="view-tile" class="view-option <?php //$viewType['tile'] ?>">&nbsp;</p>
        -->
    </div>
</div>

<div id="filter-results" class="products simple-columns">


    <?php
    foreach ( $prod_row->getModels() as $product) {
        if (is_object($product))
            echo $this->render('_card',['product'=>$product,'listType'=>$listType]);
    }
    ?>

</div>

    <?php
    echo  \frontend\components\LinkPageTmpl::widget([
        'pagination' => $prod_row->pagination,
        'options'=> [
            'id'=>'nav-tabs',
            'class' => false
        ],
        'maxButtonCount'=>6,
        'nextPageLabel' => false,
        'prevPageLabel' => false,
        'activePageCssClass' =>'active'
    ]);
    ?>




