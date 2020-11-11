<?php

namespace app\commands;


use Yii;
use yii\console\Controller;

/**
 * RBAC generator
 *  //https://anart.ru/yii2/2016/04/11/yii2-rbac-ponyatno-o-slozhnom.html
 */
class RbacController extends Controller
{
    /**
     * Generates roles
     */
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        $auth->removeAll(); //На всякий случай удаляем старые данные из БД...

        $deviceUpdate = $auth->createPermission('deviceUpdate');
        $deviceUpdate->description = 'Device update params';
        $auth->add($deviceUpdate);

        // Создадим роли админа и редактора новостей
        $admin = $auth->createRole('admin');
        $client = $auth->createRole('client');

        // запишем их в БД
        $auth->add($admin);
        $auth->add($client);


        $auth->addChild($admin, $client);
        $auth->addChild($admin, $deviceUpdate);


        $auth->assign($admin, 32);
        $auth->assign($client, 35);

        $this->stdout('Done!' . PHP_EOL);
    }
}