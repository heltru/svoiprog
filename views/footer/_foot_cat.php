<?php
use yii\helpers\Url;
if (! isset($this->params['cats_foot'])) return '';

$activeCat = (int) (isset($this->params['active_cat'])) ? $this->params['active_cat'] : 0 ;
$c = 0;
?>
    <?php foreach ($this->params['cats_foot'] as $cat){
        $c ++; if ($c >= 6) break;
        ?>
            <?php if ($activeCat == $cat->id) { ?>
                <span ><?=$cat->name?></span>
            <?php } else { ?>
                <a href="<?= Url::to(['catalog/view','id'=>$cat->id]) ?>"><?=$cat->name?></a>
            <?php } ?>
        <?php
    }
    ?>
