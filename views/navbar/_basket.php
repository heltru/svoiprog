<?php
$basket = \frontend\models\Basket::Instance();
$page =  ( isset($this->params['curr_page'])) ? $this->params['curr_page'] : '' ;
?>
<div id="basket" class="link" data-link="<?= ($page != 'basket') ? '/basket' : '#'  ?>">

    <div id="basket-inner">
        <span>
            <?php echo  Yii::$app->formatter->asDecimal($basket->getTotalCountItems(),0) ; ?>
        </span>
    </div>

</div>