<?php
use yii\helpers\Url;
/* @var $this yii\web\View
 */
$page = Yii::$app->request->get('page');

?>
<div class="content-container">
    <?=   $this->render('_bc',['category' =>$category,'url' =>$url]); ?>

    <div class="columns">
        <div id="content-main">
            <ul id="categories-img-list">
                <?php
            /*    $listCat = $category->parents_r;
                if (is_object($category->parentCat_r)) {
                    $listCat = $category->parentCat_r->parents_r;
                }*/


                foreach ($catsShowContent as $item) {

                    echo  $this->render('_parent_cat',['cat' =>$item]);
                }

                foreach ($catItems as $item) {

                    ?>
                    <?= $this->render('_card_cat',['cat' =>$item]); ?>
                <?php } ?>



            </ul>




            <div id="filter-results" class="products simple-columns">
                <?php

                foreach ( $news->getModels() as $product) {
                    if (is_object($product))
                        echo $this->render('list/_card',['product'=>$product,'listType'=>'']);
                }
                ?>
            </div>
            <h1><?=$url->h1?></h1>


        </div>

        <?php //$productsCatContentShow ?>

        <aside id="aside">

            <div id="categories-block">
                <?php
                //ex($catItems);
                ?>
                <?=   $this->render('_cats',['category' =>$category,'url' =>$url,'catItems'=>$catItems]); ?>
            </div>

            <div id="sotrudnichestvo-list">
                <p class="aside-subtitle">Предлагаем различные варианты сотрудничества</p>
                <ul>
                    <li id="zakupki" class="link" data-link="/sotrudnichestvo#zakupki"><span>Совместные закупки</span></li>
                    <li id="opt" class="link" data-link="/sotrudnichestvo#opt"><span>Оптовые заказы</span></li>
                    <li id="chertezh" class="link" data-link="/sotrudnichestvo#chertezh"><span>Индивидуальные заказы по чертежам</span></li>
                    <li id="proizvodstvo" class="link" data-link="/sotrudnichestvo#proizvodstvo"><span>Производство продукции под любым брендом</span></li>
                    <li id="agent" class="link" data-link="/sotrudnichestvo#agent"><span>Агентские продажи</span></li>
                    <li id="dropshipping" class="link" data-link="/sotrudnichestvo#dropshipping"><span>Дропшиппинг</span></li>
                </ul>
            </div>
        </aside>
    </div>
</div><!-- end of content-container -->
<script>
    $(document).ready(function () {

        $('.parentcat').click( function (e) {
            var scroll_el = $('#filter-results');

            if ($(scroll_el).length != 0) {
                $('html, body').animate({ scrollTop: $(scroll_el).offset().top }, 500); //прокручиваем до выдачи
            }
        });


    });
</script>




