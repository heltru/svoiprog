<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Настройки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settings-tmpl-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?php
    \yii\widgets\Pjax::begin([
        'id'=>'settings-grid-ajax',
    ]);
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'name',
                //   'label'=>'Params',
                'format' => 'raw',
                'value' => function($model) {

                    return Html::a(
                        $model->name,
                        Url::to(['update' ,'id'=>$model->id]) ,
                        [

                            /* 'data-pjax' => '0' ,'target'=>'_blank' */]
                    );
                },
                'filter' => Html::textInput('SettingsTmpl[name]',
                    (isset(Yii::$app->request->queryParams['SettingsTmpl']['name'] )) ?
                        Yii::$app->request->queryParams['SettingsTmpl']['name']  : ''
                    ,[ 'class'=>'form-control']),

                'headerOptions'=>[
                    'style' => 'width:20%'
                ]

            ],
      //      'id',
            [
                'attribute' => 'description',
                'format' => 'raw',
                'value' => function($model) {
                    $fp = $model->description ;
                    $str = '<div>';
                    $str .= '<span class="valFieldGrid">'.$fp.'</span>';
                    $str .= Html::textInput('editField',$model->description,
                        [
                            'data-field'=>'description',
                            'data-entity'=>'settings',
                            'data-identity'=>$model->id ,
                            'class'=>'editFieldGrid form-control']);
                    $str .= ' </div>';
                    return $str;
                },
            ]
           ,

            [
                     'attribute' => 'value',
                'format' => 'raw',
                'value' => function($model) {
                    $fp = $model->value ;
                    $str = '<div>';
                    $str .= '<span class="valFieldGrid">'.$fp.'</span>';
                    $str .= Html::textInput('editField',$model->value,
                        [
                            'data-field'=>'value',
                            'data-entity'=>'settings',
                            'data-identity'=>$model->id ,
                            'class'=>'editFieldGrid form-control']);
                    $str .= ' </div>';
                    return $str;
                },
                    'headerOptions'=>[
                            'style' => 'width:20%'
                    ]
            ]

,

            ['class' => 'yii\grid\ActionColumn',
                'template'=>'{delete}',],
        ],
    ]); \yii\widgets\Pjax::end();?>
</div>

<script>
    $(document).ready(function () {
        $('body').on('click', '.valFieldGrid',  function (e) {
            e.preventDefault();
            $(this).hide();
            $(this).parent().find('.editFieldGrid').show().focus();

        });

        $('body').on('focusout', '.editFieldGrid',  function (e) {
            $(this).hide();
            $(this).parent().find('.valFieldGrid').show();
            if ($(this).data('identity')){
                var $this = $(this);
                $.ajax({
                    type:"POST",
                    url:"<?= \yii\helpers\Url::to(['/admin/helper/default/edit-field-entity'])?>",
                    data:{
                        identity:$(this).data('identity'),
                        entity:$(this).data('entity'),
                        field:$(this).data('field'),
                        val:$(this).val(),
                        _csrfbe:yii.getCsrfToken()
                    },

                    success:function (data) {
                        $.pjax.reload('#settings-grid-ajax');

                    }
                });
            }
        });
    });
</script>