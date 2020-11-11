<?php
use frontend\models\Cat;
use yii\helpers\Url;

/*$listCat = $category->parents_r;
if (is_object($category->parentCat_r)) {
    $listCat = $category->parentCat_r->parents_r;
}*/

?>
<div id="category-menu">Категории</div>

<?= \yii\helpers\Html::hiddenInput('FilterSearch[cat_id]', $category->id) ?>
<ul id="categories-list">
    <?php

    foreach ($catItems as $item) {
        $active = ($item->id == $category->id);
        if ($active) {  ?>
            <li class="active">
                <span><?= $item->name ?></span>
            </li>
        <?php } else { ?>
            <li class="<?= ($active) ? 'active' : '' ?>">
                <a href="<?= Url::to(['catalog/viewsubcat', 'id' => $item->id]) ?>"><?= $item->name ?></a>
            </li>
        <?php }
    } ?>
</ul>