<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use app\modules\textpage\models\Textpage;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\TextpageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Страницы сайта';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="textpage-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(['id'=>'textpage-grid-view']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

          //  'id',
            [
                'attribute'=>'name',
                'label'=>'Название',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a($model->name, Url::to(['update','id'=>$model->id]) ,
                        ['style'=>'text-transform: uppercase;']);

                }
            ],
            [
                'attribute'=>'status',
                'format' => 'raw',
                'value' => function($model) {
                    return Textpage::$arrTxtStatus[ $model->status ];

                },
                'filter' => Html::dropDownList('TextpageSearch[status]',
                    (isset(Yii::$app->request->queryParams['TextpageSearch']['status'] )) ?
                        Yii::$app->request->queryParams['TextpageSearch']['status']  : '',
                    Textpage::$arrTxtStatus,['prompt'=>'нет', 'class'=>'form-control']),

            ],

            [
                'label'=>'Url',
                'format'=>'raw',

                'value'=>function ($model) {

                    if (is_object($model->url_r)) {
                        return $model->url_r->rawHref;
                    } else {
                        if (is_object($model->url_m)){
                            return $model->url_m->rawHref;
                        }
                    }
                    return '';
                }
            ],
          /*  [
                    'label'=>'Домен',
                    'format' => 'raw',
                    'value' => function($model){
                            $str = '<ul>';
                            if ( is_object(  $model->url_r )){
                                foreach (  $model->url_r->urlDomain_r as $item ){
                                    $str .=  '<li>' . $item->domain_r->name . '</li>';
                                }
                            }
                            $str .= '</ul>';
                            return $str;
                    }
            ],*/
            [
                'label'=>'Операции',
                'format'=>'raw',
                'value'=>function ($model){

                    if (is_object($model)) {
                        $ret = '<div class="btn-group" style="" role="group" aria-label="Операции">';
                        $url = \yii\helpers\Url::to(['delete','id'=>$model->id]);
                        $ret .= ' <div class="btn-group" role="group">';
                        $ret .= '<a href="'.$url.'" 
                                    title="Удалить" class="btn btn-default" aria-label="Удалить" 
                                    data-method="POST"
                                    data-pjax="0" 
                                    data-confirm="Вы уверены, что хотите удалить этот элемент?" >
                                    <span class="glyphicon glyphicon-trash"></span></a>';
                        $ret .= '</div>';
                        $ret .= ' <div class="btn-group" role="group">';
                        $url = \yii\helpers\Url::to(['update','id'=>$model->id]);
                        $ret .= '<a
                         href="'.$url.'"
title="Обновить" data-idimg="'.$model->id.'"  class="updateImg btn btn-default"
 aria-label="Обновить" data-pjax="0" 
><span class="glyphicon glyphicon-edit"></span></a>';
                        $ret .= '</div>';
                        /*$ret .= ' <div class="btn-group" role="group">';
                        $ret .= '<a class="btn btn-default ui-sortable-handle-imgv"> <span
 class="glyphicon glyphicon-sort"></span></a>';
                        $ret .= '</div>';*/
                        $ret .= ' <div class="btn-group" role="group">';
                        $class =  $model->status == Textpage::ST_OK ? 'glyphicon glyphicon-eye-open' : 'glyphicon glyphicon-eye-close';
                        $ret .= '<a data-id="'.$model->id.'" class="btn textpage_status btn-default " title="Поменять статус" aria-label="Поменять статус" ><span class="'.$class.'"></span></a>';
                        $ret .= '</div>';
                        $ret .= '</div>';
                        return $ret;

                    }

                }
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
<script>
    $(document).ready(function () {

        $('body').on('click', '.textpage_status',  function () {
            var id =  $(this).attr('data-id');//.split('_')[1] ;
            $.ajax({
                type:"POST",
                url:"<?= Url::to(['change-status']) ?>",
                data:{id:id,_csrfbe:yii.getCsrfToken()},
                success:function (data) {
                    $.pjax.reload('#textpage-grid-view');
                }
            });
        });


    });
</script>