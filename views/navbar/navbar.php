<?php
$page =  ( isset($this->params['curr_page'])) ? $this->params['curr_page'] : '' ;

use yii\helpers\Html;
?>

<div class="content-container">
    <div class="columns">
        <ul id="main-menu-items">
            <li>
                <?= ( $page == 'main' ) ? '<span>'.Yii::$app->params['siteName'].'</span>' : '<a href="/">'.Yii::$app->params['siteName'].'</a>'?>
            </li>

            <li>
                <?php
                if ($page == 'kontakty'){
                    echo '<span>Контакты</span>';
                } elseif ($page == 'main'){
                    echo '<a href="/kontakty">Контакты</a>';
                } else {
                    echo '<a class="link" data-link="/kontakty">Контакты</a>';
                }
                ?>
            </li>
        </ul>

    </div>
</div><!-- end of content-container -->