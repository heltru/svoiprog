<?php
use yii\helpers\Url;
/* @var $this yii\web\View
 */
?>
<main id="content">
    <?php

    if ( $url->h1 ) echo ' <h1 class="main-title">'.$url->h1.'</h1>';
    ?>

    <?= $textpage->text?>

    <div id="products">
        <div class="content-container">


            <div class="product-columns">
                <a class="product" href="/cms">
                    <div class="image-container">
                        <img alt="" src="/themes/news/images/product_cat.png" title=""></div>
                    <span class="product-title">CMS</span>
                    <span>Основные модули сайта</span>
                </a>
                <a class="product" href="/service">
                    <div class="image-container">
                        <img alt="" src="/themes/news/images/web_service_cat.png" title="">
                    </div>
                    <span class="product-title">web-сервисы</span>
                    <span>Новые возможности</span>
                </a>
                <a class="product" href="/lending-i-marketing">
                    <div class="image-container">
                        <img alt="" src="/themes/news/images/marketing_cat.png" title="">
                    </div>
                    <span class="product-title">Маркетинг</span>
                    <span>Коверсии заказы</span>
                </a>
                <a class="product" href="/lichnyj-kabinet">
                    <div class="image-container">
                        <img alt="" src="/themes/news/images/lichnyi_kabinet.jpg" title="">
                    </div>
                    <span class="product-title">Личные кабинеты</span>
                    <span>Образ реального мира</span>
                </a>
            </div>
        </div>
        <!-- end of content-container --><!-- end of products -->
    </div>



</main><!-- end of content -->