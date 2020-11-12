<?php
$page =  ( isset($this->params['curr_page'])) ? $this->params['curr_page'] : '' ;
?>
<div class="content-container">
    <h3>Явилась идея</h3>
    <div id="footer-form">
        <form data-owner="footer" method="post" action="<?= \yii\helpers\Url::to(['basket/send-call']) ?>" class="spnForm">
            <input type="text" name="name" placeholder="Введите Ваше имя">
            <input type="text" name="phone" placeholder="Введите Ваш телефон">
            <input type="submit" class="button" value="Жми" >
        </form>
    </div>
    <div id="footer-columns" class="columns">
        <div id="footer-links">
            <div id="footer-menu">
                <span id="footer-menu-mobile">&nbsp;</span>

                <div class="footer-links column">
                  <?php echo $this->render('_foot_cat') ?>
                </div>
            </div>
            <div id="footer-contacts" class="column">

                <a id="footer-phone" href="tel:89991002878">8 (999) 100-28-78</a>
                <p>г. Киров</p>
                <a id="footer-email" href="mailto:laneo2007@yandex.ru">laneo2007@yandex.ru</a>

            </div>
        </div>
        <div id="company-info" class="column">
            <p class="link" <?=(($this->params['curr_page'] != 'main')) ? 'data-link="/"' : ''?>>
                <img src="/images/theme/logo-bottom.png" alt="" title="" />
            </p>
            <p>svoiprog.ru</p>
            <p>Все права защищены. Использование материалов разрешено только с согласия правообладателей. Полное или частичное копирование сайта запрещено и преследуется по закону. 18+</p>
        </div>
    </div>
</div><!-- end of content-container -->