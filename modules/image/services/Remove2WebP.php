<?php


namespace app\modules\image\services;


use app\modules\image\models\Img;
use app\modules\image\models\ImgLinks;
use Yii;

class Remove2WebP
{
    public $img_link;



    private $error = false;

    private $app_delete;

    public function __construct()
    {
        $this->app_delete = new ImgDelete();

    }

    public function begin(){

        $img_link = ImgLinks::find()

            ->joinWith('img_r')
            ->andWhere(['!=','img_links.type','offer_yml'])
            ->andWhere(['!=','img_links.type','watermark'])
            ->andWhere(['img.original'=>1])
            ->all();




        foreach ($img_link as $link){

            $this->error = false;
            $this->img_link = $link;


            $this->remove2webp();



        }

    }



    public function remove2webp()
    {

        $img_r_original =   $this->img_link->img_r;

        $webp =    ImgLinks::find()
                ->joinWith('img_r')
                ->andWhere(['parent_id'=>$img_r_original->id,'webp'=>1])
                ->all();


        if (count($webp) > 1){
            $img_sort_name = [];



            foreach ($webp as $web_p_img_link){


                $img = $web_p_img_link->img_r;

                if ( in_array($img->name_image, $img_sort_name)){
                    echo $img->name_image . PHP_EOL;

                    $transaction = Yii::$app->db->beginTransaction();
                    try {


                        $web_p_img_link->delete();
                        $img->delete();



                        $transaction->commit();
                    }catch (\Exception $e) {
                        $transaction->rollBack();
                        throw $e;
                    } catch (\Throwable $e) {
                        $transaction->rollBack();
                        throw $e;
                    }

                } else {
                    $img_sort_name[] = $img->name_image;

                }


            }





        }



    }



}