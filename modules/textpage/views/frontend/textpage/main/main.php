<?php
use yii\helpers\Url;
/* @var $this yii\web\View
 */
?>
<main id="content">
    <?php

    if ( $url->h1 ) echo ' <h1 class="main-title">'.$url->h1.'</h1>';
    ?>

    <?= $textpage->text?>



</main><!-- end of content -->