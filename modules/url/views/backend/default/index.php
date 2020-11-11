<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Urls';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="url-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>



    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //     ['class' => 'yii\grid\SerialColumn'],

            //  'id',

            //'rawHref',
            [
                'attribute' => 'rawHref',
                'filter' => Html::textInput('UrlSearch[href]',
                    (isset(Yii::$app->request->queryParams['UrlSearch']['href'])) ?
                        Yii::$app->request->queryParams['UrlSearch']['href'] : '',
                    ['prompt' => 'нет', 'class' => 'form-control']
                ),
            ],
            [
                'label' => 'Сущность ',
                'format' => 'html',
                'value' => function ($model) {
                    if ($model->controller == 'catalog') {
                        return Html::a(
                            $model->controller,
                            Url::to(
                                ['/admin/catalog/default/update', 'id' => $model->identity]
                            ),
                            [
                                'target' => '_blank'
                            ]
                        );
                    }
                    if ($model->controller == 'action') {
                        return Html::a(
                            $model->controller,
                            Url::to(
                                ['/admin/action/default/update', 'id' => $model->identity]
                            ),
                            [
                                'target' => '_blank'
                            ]
                        );
                    } else
                        if ($model->controller == 'catblog') {
                            return Html::a(
                                $model->controller,
                                Url::to(
                                    ['/admin/blogcat/default/update', 'id' => $model->identity]
                                ),
                                [
                                    'target' => '_blank'
                                ]
                            );
                        } else if ($model->controller == 'filter') {
                            return Html::a(
                                $model->controller,
                                Url::to(
                                    ['/admin/filter/default/update', 'id' => $model->identity]
                                ),
                                [
                                    'target' => '_blank'
                                ]
                            );
                        } else
                            return Html::a(
                                $model->controller,
                                Url::to(
                                    ['/admin/' . $model->controller . '/default/update', 'id' => $model->identity]
                                ),
                                [
                                    'target' => '_blank'
                                ]
                            );
                },
                'filter' => Html::dropDownList('UrlSearch[controller]',
                    (isset(Yii::$app->request->queryParams['UrlSearch']['controller'])) ?
                        Yii::$app->request->queryParams['UrlSearch']['controller'] : '',
                    app\modules\url\models\Url::$entTxt, ['prompt' => 'нет', 'class' => 'form-control']),
            ],
            //  'real_canonical',
            'title',
            //   'h1',
            // 'description_meta',
            // 'redirect',
            // 'controller:url',
            // 'crs',
            // 'domain_id',
            // 'last_mod',
            [
                    'label'=>'Редиректы',
                    'format'=>'raw',
                    'value' => function ($model){
                            $s = '';
                            if (is_object($model->redirect_r)){
                                \app\modules\url\models\Url::$deph = 0;
                                $list = \app\modules\url\models\Url::checkRedirectList($model,[]);
                                //$s .= $model->redirect_r->rawHref;

                                foreach ( $list as $item){
                                   // $url_rem = Url::to(['/admin/url/default/remove-redirect','id'=>$item->id]);
                                    $url_upd = Url::to(['/admin/url/default/update','id'=>$item->id]);
                                    //$color = ($item->redirect == 0 ) ?  'red' : 'black';
                                    $s .= Html::a($item->rawHref , $url_upd);
                                    //$s .= Html::a(' <span  style="color:'.$color.'" class="glyphicon glyphicon-trash"></span>' , $url_rem);
                                    $s .= '<br>';
                                }


                               // return Html::ul(  \yii\helpers\ArrayHelper::getColumn($list,'href') );
                            }
                            return $s;
                    }

            ]
            ,

            [
                'label' => 'Операции',
                'format' => 'raw',
                'value' => function ($model) {
                    if (is_object($model)) {

                        $ret = '<div class="btn-group" style="" role="group" aria-label="Операции">';
                        $url = \yii\helpers\Url::to(['delete', 'id' => $model->id]);
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
title="Обновить" data-idimg="' . $model->id . '"  class="btn btn-default"
 aria-label="Обновить" data-pjax="0" 
><span class="glyphicon glyphicon-edit"></span></a>';
                        $ret .= '</div>';
                        /*
                                                $ret .= ' <div class="btn-group" role="group">';
                                                $ret .= '<a
                                                type="button" data-toggle="modal" data-target="#idModalRedirectUrl"
                        title="Сделать копию" data-idimg="'.$model->id.'"  class="btn btn-default updateImg"
                         aria-label="Сделать копию" data-pjax="0"><span class="glyphicon glyphicon-edit"></span></a>';
                                                $ret .= '</div>';
                          */
                        $ret .= '</div>';
                        return $ret;

                    }

                }
            ],

        ],
    ]); ?>

    <?php
    \yii\bootstrap\Modal::begin([
        'id' => 'idModalRedirectUrl',
        'header' => '<h2>Сделать копию</h2>',

    ]);

    echo Html::textInput('newHref', '', ['class' => 'form-control', 'id' => 'newHref']);

    echo '<br>';

    echo Html::a('Создать', null, ['id' => 'btnCreate', 'class' => 'btn btn-primary']);
    echo Html::hiddenInput('idUrl', null, ['id' => 'idUrl']);


    \yii\bootstrap\Modal::end();
    ?>

    <?php
    \yii\bootstrap\Modal::begin([
        'id' => 'idModalCreateRedirectUrl',
        'header' => '<h2>Добавить редирект</h2>',
    ]);
    echo Html::textInput('createHref', '', ['class' => 'form-control', 'id' => 'createHref']);
    echo '<br>';
    echo Html::a('Создать', null, ['id' => 'btnCreateRedirect', 'class' => 'btn btn-primary']);
    echo Html::hiddenInput('idUrlCreate', null, ['id' => 'idUrlCreate']);

    \yii\bootstrap\Modal::end();
    ?>

</div>

<script>
    $(document).ready(function () {
//single form

        $("#btnCreateRedirect").click(function (e) {
            var href = $('#newHref').val();
            var id = $('#idUrl').val();
            $.ajax(
                {
                    type: "GET",
                    url: "<?= Url::to(['/url/double-url'])?>",
                    data: {href: href},
                    success: function (data) {

                        if (typeof data == 'object') {
                            if (data.status == 200) {
                                $.ajax({
                                    type: "POST",
                                    url: "<?= Url::to(['/url/create-redirect-url']) ?>",
                                    data: {id: id, href: href},
                                    success: function (data) {
                                        if (typeof data == 'object') {
                                            if (data.status == 200) {
                                                window.location.href = data.url;
                                            }
                                            if (data.status == 500) {
                                                $("#newHref").css('color', 'red');
                                            }
                                        }
                                    }
                                });
                            }
                            if (data.status == 500) {
                                $("#newHref").css('color', 'red');
                            }
                        }
                    }
                }
            );
        });


        //each record

        $('.updateImg').click(function (e) {

            var url = $(this).parent().parent().parent().parent().find('td:first').html();
            var id = $(this).data('idimg');

            $('#newHref').val(url);
            $('#idUrl').val(id);
        });

        $("#btnCreate").click(function (e) {
            var href = $('#newHref').val();
            var id = $('#idUrl').val();
            $.ajax(
                {
                    type: "GET",
                    url: "<?= Url::to(['/admin/url/url/double-url'])?>",
                    data: {href: href},
                    success: function (data) {

                        if (typeof data == 'object') {
                            if (data.status == 200) {
                                $.ajax({
                                    type: "POST",
                                    url: "<?= Url::to(['/url/create-redirect-url']) ?>",
                                    data: {id: id, href: href},
                                    success: function (data) {
                                        if (typeof data == 'object') {
                                            if (data.status == 200) {
                                                window.location.href = data.url;
                                            }
                                            if (data.status == 500) {
                                                $("#newHref").css('color', 'red');
                                            }
                                        }
                                    }
                                });
                            }
                            if (data.status == 500) {
                                $("#newHref").css('color', 'red');
                            }
                        }
                    }
                }
            );
        });

        //btnCreate
    });
</script>
