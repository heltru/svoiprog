<?php

use yii\helpers\Html;

$id = $model->id;
$formName = 'Img';
$imgClass = 'imgFile';
$fileInputClass = 'imgFile';
if ($new) {
    $formName = 'Imgnew';
    $id = 1;
    $imgSrc = '/' . \app\modules\image\models\Img::NOIMG;
    $class = 'imgPlane';
    $imgClass = 'imgCropNew imgCropCommon';
    $fileInputClass = 'imgFile fileInputNew';
    //$model->watermark = 1;$model->optimize = 1;// $model->harshness = 1;
    //  $model->webp = 1;
} else {
    $imgSrc = '/' . $model->name_image;
    $class = '';
    $imgClass = 'imgCropOld imgCropCommon';
}

$settings = \Yii::$app->getModule('settings');
$tinypng_count = (int)$settings->getVar('tinypng_count');

?>
<div class="row  <?= ' ' . ($new) ? 'itemImg' : 'lineImg' ?>  "
    <?= (!$new) ? 'idsort="' . $model->id . '"' : '' ?> >
    <div class="col-lg-5 col-xs-12 <?= $class ?>">

        <?php

        echo Html::img($imgSrc, ['class' => 'imgPrevUpload img-responsive ' . $imgClass]);
        ?>

    </div>

    <div class="col-lg-3 col-xs-12">

        <div class="row">

            <div class="col-xs-12">
                <?php echo Html::fileInput($formName . '[' . $id . '][file]', null,
                    ['accept' => 'image/*', 'class' => $fileInputClass]); ?>
                <span style="display: none" class="raw_caption">размер <span class="raw_size">123 kb</span></span>
            </div>


            <div class="col-xs-6">
                <?php $update_img = $model->update_img;
                echo Html::label($model->getAttributeLabel('update_img'), null, ['class' => 'control-label']);
                echo Html::checkbox($formName . '[' . $id . '][update_img]', $update_img, ['class' => 'form-control updateImg']);
                ?>
            </div>
            <div class="col-xs-6">
                <?php
                echo Html::label($model->getAttributeLabel('watermark'), null, ['class' => 'control-label']);
                echo Html::checkbox($formName . '[' . $id . '][watermark]', $model->watermark, ['class' => 'form-control imgWatermark']);
                ?>
            </div>
            <div class="col-xs-6">
                <?php
                echo Html::label($model->getAttributeLabel('resize'), null, ['class' => 'control-label']);
                echo Html::checkbox($formName . '[' . $id . '][resize]', $model->resize, ['class' => 'form-control imgResize']);
                ?>
            </div>
            <div class="col-xs-6">
                <?php
                // echo Html::label( $model->getAttributeLabel('logo_r_b') ,null,['class'=>'control-label']);
                //  echo Html::checkbox( $formName.'['.$id.'][logo_r_b]',$model->logo_r_b,['class'=>'form-control imgLogo' ]);
                ?>
            </div>

            <div class="col-xs-6">
                <?php
                echo Html::label($model->getAttributeLabel('optimize'), null, ['class' => 'control-label']);
                echo Html::checkbox($formName . '[' . $id . '][optimize]', $model->optimize, ['class' => 'form-control imgOptimize',
                    'title' => ($tinypng_count >= 500) ? 'лимит ключа исчерпан' : $tinypng_count . '-500',
                    'disabled' => ($tinypng_count >= 500)]);
                ?>
            </div>
            <div class="col-xs-6">
                <?php
                //     echo Html::label( $model->getAttributeLabel('harshness') ,null,['class'=>'control-label']);
                //     echo Html::checkbox( $formName.'['.$id.'][harshness]',$model->harshness,['class'=>'form-control imgHarshness' ]);
                ?>
            </div>
            <div class="col-xs-6">
                <?php
                //  echo Html::label( $model->getAttributeLabel('blur') ,null,['class'=>'control-label']);
                //   echo Html::checkbox( $formName.'['.$id.'][blur]',$model->blur,['class'=>'form-control imgBlur' ]);
                ?>
            </div>


            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-12">
                        <?php echo Html::dropDownList($formName . '[' . $id . '][size]',
                            (!$model->isNewRecord) ? (string)($model->width . '_' . $model->height) : null,
                            $tab_content_header, ['class' => 'form-control imgSize imgSizeNew', 'style' => 'margin-top: 0.5em;']) ?>
                    </div>
                </div>
                <div class="row">
                    <?php
                    echo Html::activeHiddenInput($model, 'width', ['name' => $formName . '[' . $id . '][width]', 'class' => 'imgWidth']);
                    echo Html::activeHiddenInput($model, 'height', ['name' => $formName . '[' . $id . '][height]', 'class' => 'imgHeight']);
                    echo Html::activeHiddenInput($model, 'crop_x', ['name' => $formName . '[' . $id . '][crop_x]', 'class' => 'imgCropX']);
                    echo Html::activeHiddenInput($model, 'crop_y', ['name' => $formName . '[' . $id . '][crop_y]', 'class' => 'imgCropY']);
                    echo Html::activeHiddenInput($model, 'crop_width', ['name' => $formName . '[' . $id . '][crop_width]', 'class' => 'imgCropWidth']);
                    echo Html::activeHiddenInput($model, 'crop_height', ['name' => $formName . '[' . $id . '][crop_height]', 'class' => 'imgCropHeight']);
                    echo Html::activeHiddenInput($model, 'wrap_width', ['name' => $formName . '[' . $id . '][wrap_width]', 'class' => 'imgWrapWidth']);
                    echo Html::activeHiddenInput($model, 'wrap_height', ['name' => $formName . '[' . $id . '][wrap_height]', 'class' => 'imgWrapHeight']);
                    echo Html::activeHiddenInput($model, 'ord', ['name' => $formName . '[' . $id . '][ord]', 'class' => 'imgOrd']);


                    ?>
                </div>
            </div>

        </div>


    </div>
    <div class="col-lg-4 col-xs-12">

        <div class="row">
            <div class="col-xs-12">
                <?php $alt = $model->alt;
                echo Html::label($model->getAttributeLabel('alt'), null, ['class' => 'control-label']);
                echo Html::textarea($formName . '[' . $id . '][alt]', $alt, ['class' => 'form-control imgAlt']);
                ?>
            </div>
            <div class="col-xs-12 ">
                <?php
                echo Html::label($model->getAttributeLabel('title'), null, ['class' => 'control-label']);
                $title = $model->title;
                echo Html::textarea($formName . '[' . $id . '][title]', $title, ['class' => 'form-control imgTitle']);
                ?>
            </div>


            <?php if (FALSE === $new) { ?>
                <div class="col-xs-6 pull-right" style="margin-top: 1em;">
                    <div class="btn-group pull-right" role="group">
                        <a class="btn btn-default imgSort">
                            <span class="glyphicon glyphicon-sort"></span>
                        </a>
                        <a class="btn btn-default imageDeleteN"
                           href="<?= \yii\helpers\Url::to(['/admin/image/default/delete', 'id' => $model->id]) ?>"
                           data-id="<?= $model->id ?>">
                            <span class="glyphicon glyphicon-trash "></span>
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
