<?php
use yii\helpers\Url;
if (! isset($this->params['cats_header'])) return '';
$activeCat = (int) (isset($this->params['active_cat'])) ? $this->params['active_cat'] : 0 ;
$curr_page = (int) (isset($this->params['curr_page'])) ? $this->params['curr_page'] : '' ;

?>
<ul id="product-menu-items" class="columns">
<?php foreach ($this->params['cats_header'] as $cat){ ?>
    <li id="<?=$cat->li_id_tag ?>">
        <?php

        if (is_object($cat->seoMenuItem_r)){
            $cat_is_active = $cat->seo_menu_item == $activeCat;
        } else {
            $cat_is_active = $activeCat == $cat->id;
        }


        if ($cat_is_active) { ?>
            <span ><?=$cat->name?></span>
        <?php }

        else {

            if (is_object($cat->seoMenuItem_r)){
                $href =  Url::to(['/news/cat/viewsubcat','id'=>$cat->seo_menu_item]);
            } else {
                $href =  Url::to(['/news/cat/view','id'=>$cat->id]);
            }

            ?>
            <a href="<?= $href?>"><?=$cat->name?></a>
        <?php } ?>

    </li>
    <?php
}
?>

</ul>




