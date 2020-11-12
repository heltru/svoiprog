<?php

namespace app\modules\news\service;


use app\modules\image\services\AttImg;
use app\modules\image\services\Exception;
use app\modules\news\models\NewsBlock;
use app\modules\url\models\Url;
use yii\db\Transaction;
use yii\web\BadRequestHttpException;

class News
{

    public static $url_controller = 'news';
    public static $url_module = 'news';

    public static function delete($news_id)
    {
        $item = \app\modules\news\models\News::findOne($news_id);
        if ($item !== null) {

            $transaction = \Yii::$app->db->beginTransaction(Transaction::SERIALIZABLE);
            try {


                foreach ($item->newsBlocks_r as $block) {
                    self::deleteBlock($block->id);
                }

                $item->delete();

                AttImg::delAllRef($news_id, self::$url_module);
                Url::deleteAll(['controller' => 'news', 'action' => 'view', 'identity' => $news_id]);


                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollBack();
                throw new \Exception($e->getMessage(), 0, $e);
            }

        }


    }


    public static function getBlocksQuery($news_id, $block_id = null)
    {
        $query = NewsBlock::find();
        $query->where(['news_id' => $news_id])->orderBy('ord');
        if ($block_id) {
            $query->andWhere(['id' => $block_id]);
        }
        return $query;


    }


    public static function deleteBlock($block_id)
    {

        $model = NewsBlock::findOne(['id' => $block_id]);
        if ($model !== null) {

            $transaction = \Yii::$app->db->beginTransaction(Transaction::SERIALIZABLE);
            try {

                $model->delete();
                AttImg::delAllRef($block_id, 'news_bl');
                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollBack();
                throw new \Exception($e->getMessage(), 0, $e);
            }


        }
    }


}