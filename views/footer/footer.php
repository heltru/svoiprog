<?php
$page =  ( isset($this->params['curr_page'])) ? $this->params['curr_page'] : '' ;
?>
<div class="content-container">
    <h3>Сотрудники оптового отдела проконсультируют Вас по всем интересующим вопросам</h3>
    <div id="footer-form">
        <form data-owner="footer" method="post" action="<?= \yii\helpers\Url::to(['basket/send-call']) ?>" class="spnForm">
            <input type="text" name="name" placeholder="Введите Ваше имя">
            <input type="text" name="phone" placeholder="Введите Ваш телефон">
            <input type="submit" class="button" value="Получить консультацию" >
        </form>
    </div>
    <div id="footer-columns" class="columns">
        <div id="footer-links">
            <div id="footer-menu">
                <span id="footer-menu-mobile">&nbsp;</span>
                <div class="footer-links column">
                    <span class="link" data-link="/o-nas">О компании</span>
                    <span class="link" data-link="/sotrudnichestvo">Сотрудничество</span>
                    <span class="link" data-link="/novosti">Новости</span>
                    <span class="link" data-link="/akcii">Акции</span>
                    <span class="link" data-link="/kontakty">Контакты</span>
                </div>
                <div class="footer-links column">
                    <span class="link" data-link="/oplata">Условия оплаты</span>
                    <span class="link" data-link="/akcii">Акции</span>
                    <span class="link" data-link="/garantii-i-servis">Гарантии и сервис</span>
                    <span class="link" data-link="/help">Помощь</span>
                </div>
                <div class="footer-links column">
                  <?php echo $this->render('_foot_cat') ?>
                </div>
            </div>
            <div id="footer-contacts" class="column">

                <a id="footer-phone" href="tel:84952553740">8 (495) 255-37-40</a>
                <p>г. Киров, пер. Химический, д.1</p>
                <a id="footer-email" href="mailto:info@kompany.ru">opt@samogon-optom.ru</a>
                <a class="doc" href="/site/file-conf">Политика конфиденциальности</a>

            </div>
        </div>
        <div id="company-info" class="column">
            <p class="link" <?=(($this->params['curr_page'] != 'main')) ? 'data-link="/"' : ''?>>
                <img src="/images/theme/logo-bottom.png" alt="" title="" />
            </p>
            <p>samogon-optom.ru</p>
            <p>Все права защищены. Использование материалов разрешено только с согласия правообладателей. Полное или частичное копирование сайта запрещено и преследуется по закону. 18+</p>
        </div>
    </div>
</div><!-- end of content-container -->