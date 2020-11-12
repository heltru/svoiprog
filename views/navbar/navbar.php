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


        </ul>

    </div>
</div><!-- end of content-container -->