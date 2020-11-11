<?php
use app\modules\smeta\models\ConstructionObject;
use app\modules\smeta\models\Category;
use app\modules\smeta\models\Smeta;
use yii\helpers\Url;

$isAdmin = \app\modules\helper\models\Helper::getIsAdmin(Yii::$app->user->id);

 $myRkItems[] =  [
        'label'=>'добавить','icon'=>'plus',
        'url'=>'#',//Url::to(['/admin/smeta/construction-object/create']),
        'active' =>$this->context->route == '/admin/smeta/construction-object/create',
        'options'=>['data-role'=>'msbr_new-object']
 ];



//$myRkItems = [];



foreach (Category::findAll(['user_id'=>Yii::$app->user->id]) as $item){
    $sitems = [];
    foreach ( $item->objects_r as $subitem){

        $ssitems = [];
        if (!empty( $this->params['curr_object_id']) && $subitem->id == $this->params['curr_object_id']){
            foreach (Smeta::findAll(['object_id'=>$subitem->id]) as $ssitem){
                $ssitems[] = [
                    'label'=> $ssitem->name,
                    'icon'=>' ',
                    'active' =>( $this->context->route == 'admin/smeta/smeta/update'
                        &&   $ssitem->id == $this->params['curr_smeta_id'] ),

                    'url'=>Url::to(['/admin/smeta/smeta/update','id'=>$ssitem->id]),

                ];
            }
            $ssitems[] =  [
                'label'=>'+ добавить','icon'=>' ',
                'url'=>'#',//Url::to(['/admin/smeta/construction-object/create']),
                'active' =>$this->context->route == '/admin/smeta/construction-object/create',
                'options'=>['data-role'=>'msbr_smeta-add','data-object_id'=>$subitem->id]
            ];

        }


        $sitems[] = [
            'label'=> $subitem->name,
            'icon'=>' ',
            'items' => $ssitems,
            'active' =>(
            ( $this->context->route == 'admin/smeta/smeta/update' &&
                $subitem->id == $this->params['curr_object_id']
            )
            ||  (
                $this->context->route == 'admin/smeta/construction-object/update' &&
                $subitem->id == $this->params['curr_object_id']
                )
        ),

            'url'=>Url::to(['/admin/smeta/construction-object/update','id'=>$subitem->id]),

        ];
    }

    if (!empty($this->view->params['curr_object_id'])   ){

    }

    $myRkItems[] = [
        'label'=>$item->name,
        'icon'=>' ',
        'active' =>
            (
                    (
                            $this->context->route == 'admin/smeta/construction-object/update' ||
                            $this->context->route == 'admin/smeta/smeta/update'
                    )  && $item->id == $this->params['curr_category_id']
            )
        ,
        'items' => $sitems,
       // 'url'=>Url::to(['/admin/smeta/construction-object/update','id'=>$item->id]),
        'options'=>['data-role'=>'msbr_object-item'],
//        'template'=>'<a href="{url}">{icon} {label}
//                            <span data-role="msbr_object-item-link" class="pull-right-container">
//                                <i class="fa fa-angle-left pull-right"></i>
//                            </span>
//            </a>',

    ];


}

?>
<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="/images/round-account-button-with-user-inside.png" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p><?= Yii::$app->getModule('app')->getCompanyName()?> </p>

                <a href="#"><i class="fa fa-circle text-success"></i>
                    <?= Yii::$app->formatter->asDecimal(
                        Yii::$app->getModule('app')->getBalance(), 0) ?>
                </a>
            </div>
        </div>


        <?php


        $all_items = [

          //  ['label' => 'Справочник', 'options' => ['class' => 'header']],
            [
                'label' => 'Мои объекты',
                'template'=>'<a  href="{url}">{icon} {label} 
 
<span class="pull-right-container">

<i class="fa fa-angle-left pull-right"></i>
   
</span></a>',
          //      'template'=>'<span>{label}</span>',
                'icon' => ' fa-legal',
            //    'url' =>'#',
                //'url' =>  ['/admin/block/index'],
                    $this->context->route == 'admin/smeta/smeta/update' ||
                    $this->context->route == 'admin/smeta/construction-object/update'
                ,
                'items' => $myRkItems,


            ],

//            [
//                'label' => 'Сметы',
//                //'icon' => 'dashboard',
//                'url' => ['/admin/smeta/smeta/index'],
//                //    'visible' => ! $isAdmin,
//                'active' => $this->context->route == 'admin/smeta/smeta/index' &&
//                    Yii::$app->controller->action->id == 'index'
//            ],



//            [
//                'label' => 'Состояние аппарата',
//                'icon' => 'dashboard',
//                'url' => ['/admin/alco/device/index'],
//                'visible' => ! $isAdmin,
//                'active' => $this->context->route == 'admin/alco/device/index' && Yii::$app->controller->action->id == 'index'
//            ],
            ['label' => 'Справочник', 'options' => ['class' => 'header']],
            [
                'label' => 'Разделы',
                //'icon' => 'dashboard',
                'url' => ['/admin/smeta/razdel-info/index'],
               // 'visible' => ! $isAdmin,
                'active' => $this->context->route == 'admin/smeta/razdel-info/index' &&
                    Yii::$app->controller->action->id == 'index'
            ],
            [
                'label' => 'Позиции',
                //'icon' => 'dashboard',
                'url' => ['/admin/smeta/position-info/index'],
            //    'visible' => ! $isAdmin,
                'active' => $this->context->route == 'admin/smeta/position-info/index' &&
                    Yii::$app->controller->action->id == 'index'
            ],



        ];
        $admin_items = [
//            ['label' => 'Admin', 'options' => ['class' => 'header']],
//            [
//                'label' => 'Аппараты',
//                'icon' => 'dashboard',
//                'url' => ['/admin/alco/device/index'],
//                'active' => $this->context->route == 'admin/alco/device/index' &&
//                    Yii::$app->controller->action->id == 'index'
//            ],


        ];

        if ($isAdmin) {
            $all_items = array_merge($all_items, $admin_items);
        }
        ?>
        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
                'items' => $all_items,
            ]
        ) ?>

    </section>

</aside>
