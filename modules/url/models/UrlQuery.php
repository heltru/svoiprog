<?php
/**
 * Created by PhpStorm.
 * User: o.trushkov
 * Date: 09.04.18
 * Time: 14:46
 */

namespace app\modules\url\models;


use yii\db\ActiveQuery;
use Yii;

class UrlQuery extends ActiveQuery
{



    public function domain(){

        $domain = Yii::$app->getModule('domain');

        $this->innerJoin('url_domain','url.id = url_domain.url_id');
        $this->andWhere(['url_domain.domain_id'=>$domain->getDomainId()]);

        return $this;
    }

}