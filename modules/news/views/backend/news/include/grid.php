<?php
/**
 * Created by PhpStorm.
 * User: o.trushkov
 * Date: 10.05.17
 * Time: 12:20
 */

use app\modules\news\models\NewsBlock;
use yii\helpers\Url;

\yii\widgets\Pjax::begin([
    'id' => 'news-descr-grid-ajax',
]);

echo \app\components\grid\GridView::widget([
    'dataProvider' => $dpDescr,
    'idTBody' => 'newsDescrRowView',
    'layout' => "{items}\n{pager}",
    'emptyText' => '',
    'columns' => [
       // ['class' => 'yii\grid\SerialColumn'],
[
            'label' => 'Операции',
            'format' => 'raw',
            'value' => function ($model) {
                $class = !$model->status ? 'glyphicon glyphicon-eye-open' : 'glyphicon glyphicon-eye-close';
                $str = '<div class="btn-group" role="group"  >';
                $str .= '<a  class="btn btn-default ">
<span  class="glyphicon glyphicon-sort ui-sortable-handle-recprod"></span>
</a>';
                $str .= '<a  data-id="' . $model->id . '"  class="btn status_prod_descr btn-default ">
                <span class="' . $class . '"></span></a>';
                $str .= '<a  class="btn btn-default rem_prod_descr" id="' . $model->id . '">
<span class="glyphicon glyphicon-trash"></span>
</a>
<a target="_blank" title="редактировать блок" href="' . Url::to(['news-descr-update', 'id' => $model->id, 'red' => 'prod']) . '" class="btn btn-default"    ><span class="glyphicon glyphicon-edit"></span> </a>

 </div>';
                return $str;
            }
        ],
        'name',
        [
            'attribute' => 'type_block',
            'value' => function ($model) {
                return (isset(NewsBlock::$arrTxtBlock[$model->type_block])) ? NewsBlock::$arrTxtBlock[$model->type_block] : '';
            }
        ],
        [
            'label' => 'Описание',
            'format' => 'html',
            'value' => function ($model, $key, $index, \yii\grid\DataColumn $grid) {
                $news = $model->news_r;
                $url = $news->url_r;
                $news_blocks_query = \app\modules\news\service\News::getBlocksQuery($news->id, $model->id);
                $news_blocks = $news_blocks_query->all();

                $c = $grid->grid->view->renderFile('@app/modules/news/views/frontend/news/include/blocks.php',
                    ['news' => $news, 'url' => $url, 'news_blocks' => $news_blocks]);
                return $c;
                return \yii\helpers\StringHelper::truncate($model->desc, 200);
            }
        ],

        [
            'label' => 'Операции',
            'format' => 'raw',
            'value' => function ($model) {
                $class = !$model->status ? 'glyphicon glyphicon-eye-open' : 'glyphicon glyphicon-eye-close';
                $str = '<div class="btn-group" role="group"  >';
                $str .= '<a  class="btn btn-default ">
<span  class="glyphicon glyphicon-sort ui-sortable-handle-recprod"></span>
</a>';
                $str .= '<a  data-id="' . $model->id . '"  class="btn status_prod_descr btn-default ">
                <span class="' . $class . '"></span></a>';
                $str .= '<a  class="btn btn-default rem_prod_descr" id="' . $model->id . '">
<span class="glyphicon glyphicon-trash"></span>
</a>
<a target="_blank" title="редактировать блок" href="' . Url::to(['news-descr-update', 'id' => $model->id, 'red' => 'prod']) . '" class="btn btn-default"    ><span class="glyphicon glyphicon-edit"></span> </a>

 </div>';
                return $str;
            }
        ]
    ],
]);
\yii\widgets\Pjax::end();
?>
<script>
    $(document).ready(function () {


        $("#newsDescrRowView").sortable({
            items: "tr",
            update: function () {
                var info = $(this).sortable("serialize", {'attribute': 'idsort'});
                //   console.log(info);
                $.ajax({
                    type: "POST",
                    url: "<?= \yii\helpers\Url::to(['news-descr-sort'])?>",
                    data: {_csrfbe: yii.getCsrfToken(), info: info},
                    context: document.body
                });
            },
            placeholder: "ui-state-highlight-group",
            handle: $(".ui-sortable-handle-recprod")


        });
    });
</script>
