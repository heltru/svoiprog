<?php
use yii\helpers\Url;
?>
<nav id="top-menu">
    <div id="main-menu">
        <span id="main-menu-mobile">&nbsp;</span>
        <?=$this->render('//navbar/navbar');?>
    </div>
    <div id="header-middle">
        <div class="content-container">
            <div class="columns">
                <?php if (($this->params['curr_page'] != 'main')) {  ?>
                <p class="logo link"  data-link="/" >
                    <img src="/themes/news/images/logo-top.png" alt="" title="" />
                    <span>Цифровая компания</span>
                </p>
                <?php } else { ?>
                    <p class="logo" >
                        <img src="/themes/news/images/logo-top.png" alt="" title="" />
                        <span>Цифровая компания</span>
                    </p>
                <?php } ?>



                <a class="phone" href="tel:89991002878"><span>8(999)</span>100-28-78</a>

            </div>
        </div> <!-- end of content-container -->
    </div>
    <div id="product-menu">
        <div class="content-container">
            <?=$this->render('//navbar/_catitem')?>
        </div> <!-- end of content-container -->
    </div>
</nav>