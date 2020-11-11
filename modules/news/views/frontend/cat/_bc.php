<?php
use yii\helpers\Url;

/* @var $this yii\web\View
 * @var $category frontend\models\Cat
 * @var $url common\models\Url
 */
?>
<ul class="breadcrumb" class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList" >
    <li>
        <a href="/"  >
            <?=Yii::$app->params['siteName']?>
        </a>
    </li>
    <?php
    if ( is_object(  $category->parentCat_r )) {
        $bcsubcat =  $category->parentCat_r->name;
        $bc =  $category->name;
        $url_p = Url::to( ['catalog/view','id'=>$category->parentCat_r->id  ]);
        $url_s = Url::to( ['catalog/viewsubcat','id'=>$category->id],true);
    ?>

        <li
                itemprop="itemListElement" itemscope
                itemtype="http://schema.org/ListItem"
        >
            <a
                itemprop="item"
                href="<?=$url_p?>">
                <?=$bcsubcat?>
            </a>
            <meta itemprop="name" content="<?= $bcsubcat ?>"/>
            <meta itemprop="position" content="1" />
        </li>

        <li
                itemprop="itemListElement" itemscope
                itemtype="http://schema.org/ListItem"
        >
            <meta itemprop="item" content="<?=$url_s?>" />
            <span itemprop="name"><?=$bc?></span>
            <meta itemprop="position" content="2" />
        </li>
    <?php }  else {
        $url_p = Url::to( ['catalog/view','id'=>$category->id ],true);
        $bc =    $category->name;
        ?>

        <li
                itemprop="itemListElement" itemscope
                itemtype="http://schema.org/ListItem"
        >
            <meta itemprop="item" content="<?=$url_p?>" />
            <span itemprop="name"  ><?= $bc ?></span>
            <meta itemprop="position" content="3" />

        </li>


    <?php } ?>
</ul>
