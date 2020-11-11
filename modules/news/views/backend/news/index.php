<?php

use app\modules\news\models\News;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */

/* @var $dataProvider yii\data\ActiveDataProvider */
$this->registerJsFile('/js/jquery-ui.min.js', ['position' => yii\web\View::POS_END]);
$this->registerJsFile('/js/jquery.ui.touch-punch.min.js', ['position' => yii\web\View::POS_END]);
$this->title = 'Новости';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php
    Pjax::begin([
        'id' => 'news-grid-ajax',
    ]);
    ?>
    <?= \app\components\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'newsGrid',
        'idTBody' => 'newsRowView',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function ($model) {

                    return Html::a(
                        $model->name,
                        \yii\helpers\Url::to(['update', 'id' => $model->id]),
                        [
                            //    'style'=>'    text-transform: uppercase;'
                            /* 'data-pjax' => '0' ,'target'=>'_blank' */]
                    );
                },
                'filter' => Html::textInput('NewsSearch[name]',
                    (isset(Yii::$app->request->queryParams['NewsSearch']['name'])) ?
                        Yii::$app->request->queryParams['NewsSearch']['name'] : ''
                    , ['class' => 'form-control']),

            ],

            [
                'attribute' => 'date_public',
                'format' => ['date', 'dd.MM.Y'],
                'filter' => false
            ]
            ,

            [
                'attribute' => 'type',
                'value' => function ($model) {
                    if ($model->type !== null)
                        return News::$arrTxtType[$model->type]; else return '';
                },
                'filter' => Html::dropDownList('NewsSearch[type]',
                    (isset(Yii::$app->request->queryParams['NewsSearch']['type'])) ?
                        Yii::$app->request->queryParams['NewsSearch']['type'] : '',
                    News::$arrTxtType
                    , ['prompt' => 'нет', 'class' => 'form-control']),

            ],
            [
                'attribute' => 'first',
                'value' => function ($model) {
                    return News::$arrTxtFirst [$model->first];
                },
                'filter' => Html::dropDownList('NewsSearch[first]',
                    (isset(Yii::$app->request->queryParams['NewsSearch']['first'])) ?
                        Yii::$app->request->queryParams['NewsSearch']['first'] : '',
                    News::$arrTxtFirst
                    , ['prompt' => 'нет', 'class' => 'form-control']),

            ],
            [
                'label' => 'Категория',
                'format' => 'raw',
                'value' => function (\app\modules\news\models\News $model) {
                    if (is_object($model->newsCat_r)) {
                        return $model->newsCat_r->name;
                    }
                    return '';
                }
            ],

            [
                'label' => 'Операции',
                'format' => 'raw',
                'value' => function ($model) {
                    if (is_object($model)) {

                        $ret = '<div class="btn-group" style="" role="group" aria-label="Операции">';
                        $url = \yii\helpers\Url::to(['/news/delete', 'id' => $model->id]);
                        $ret .= ' <div class="btn-group" role="group">';
                        $ret .= '<a href="' . $url . '" 
                                    title="Удалить" class="btn btn-default" aria-label="Удалить" 
                                    data-method="POST"
                                    data-pjax="0" 
                                    data-confirm="Вы уверены, что хотите удалить этот элемент?" >
                                    <span class="glyphicon glyphicon-trash"></span></a>';
                        $ret .= '</div>';
                        $ret .= ' <div class="btn-group" role="group">';
                        $url = \yii\helpers\Url::to(['update', 'id' => $model->id]);
                        $ret .= '<a
                        href="' . $url . '"
title="Обновить" data-idimg="' . $model->id . '"  class="updateImg btn btn-default"
 aria-label="Обновить" data-pjax="0" 
><span class="glyphicon glyphicon-edit"></span></a>';
                        $ret .= '</div>';
                        $ret .= ' <div class="btn-group" role="group">';
                        $class = $model->status == News::ST_OK ? 'glyphicon glyphicon-eye-open' : 'glyphicon glyphicon-eye-close';
                        $ret .= '<a data-id="' . $model->id . '" class="btn news_status btn-default " title="Поменять статус" aria-label="Поменять статус" ><span class="' . $class . '"></span></a>';
                        $ret .= '</div>';
                        $ret .= ' <div class="btn-group" role="group">';
                        $ret .= '<a  class="btn btn-default ">
<span  class="glyphicon glyphicon-sort ui-sortable-handle-recprod"></span>
</a>';
                        $ret .= '</div>';
                        $ret .= '</div>';
                        return $ret;

                    }

                }
            ],
        ],
    ]);
    Pjax::end(); ?>
</div>
<script>
    $(document).ready(function () {


        $("#newsRowView").sortable({
            items: "tr",
            update: function () {
                var info = $(this).sortable("serialize", {'attribute': 'idsort'});
                //   console.log(info);
                $.ajax({
                    type: "POST",
                    url: "<?= \yii\helpers\Url::to(['/news/prod-news-sort'])?>",
                    data: {_csrfbe: yii.getCsrfToken(), info: info},
                    context: document.body,
                });
            },
            placeholder: "ui-state-highlight-group",
            handle: $(".ui-sortable-handle-recprod")
        });

        $('body').on('click', '.news_status', function () {
            var id = $(this).attr('data-id');//.split('_')[1] ;
            $.ajax({
                type: "POST",
                url: "<?= Url::to(['change-status']) ?>",
                data: {id: id, _csrfbe: yii.getCsrfToken()},
                success: function (data) {
                    $.pjax.reload('#news-grid-ajax');
                }
            });
        });

    });
</script>
