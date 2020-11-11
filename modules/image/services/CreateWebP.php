<?php
namespace app\modules\image\services;


use app\modules\image\models\Img;
use app\modules\image\models\ImgLinks;
use yii\helpers\FileHelper;
use Yii;

class CreateWebP
{

    public $img_link;
    private $new_name_image;
    private $new_file_name;
    private $new_src;



    private $error = false;

    private $app_convert;

    public function __construct()
    {
        $this->app_convert = new AttImg();

    }

    public function begin(){

        $img_link = ImgLinks::find()

            ->joinWith('img_r')
            ->andWhere(['>','img.width',0])// annd filter
            ->andWhere(['!=','img_links.type','offer_yml'])
            ->andWhere(['!=','img_links.type','watermark'])
            ->andWhere(['!=','img.webp',1])
            ->andWhere(['!=','img.fullsize',1])
            ->andWhere(['!=','img.original',1])

           ->all();




        foreach ($img_link as $link){

            $this->error = false;
            $this->img_link = $link;


            $this->convertWebP($link->img_r, $link->type,$link->id_type );



        }

    }



    public function convertWebP($img,$type,$type_id)
    {

        $imgF = $img;

        if ($imgF === null) {
            var_dump('not find full size');
            exit;
            return;
        }



        $env_dir = Yii::$app->params['path_env'] . 'web/';
        $path_img_source = $env_dir . $imgF->name_image;

        if (! file_exists($path_img_source)){
            return;
        }

        $file = [ 'name'=>$imgF->filename,
            'content'=> file_get_contents($path_img_source)];

        $appOpt = new ImageCompressor([$file]);
        $appOpt->setting['web_b_convert'] = 1;
        $appOpt->optimize_photo();

        $imgLinkWebp = $appOpt->getResult();

        $img_new = clone $img;
        $img_new->id = null;
        $img_new->setIsNewRecord(true);
        $img_new->webp = 1;
        $img_new->parent_id = $img->parent_id;


        $pathinfo = pathinfo($img->filename);
        $filename = $pathinfo['filename'] . '.webp';
        $dir = 'uploads/' . $type . '/' . $type_id;


        $file_path = $dir . '/' . $filename;
        $webp_link  = '';
        foreach ($imgLinkWebp['urls'] as $url_res){
            if( strpos($url_res['link'],'.webp') !== false){
                $webp_link = $url_res['link'];
            }
        }

        if ($webp_link){
            file_put_contents($env_dir . $file_path,file_get_contents($webp_link));
            //chmod($env_dir . $file_path, 0660);

            $img_new->filename = $filename;
            $img_new->name_image = $file_path;


            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ( $img_new->save()){

                    $img->webp = 0;
                    $img->update(false,['webp']);


                    $img_link = new ImgLinks();
                    $img_link->type = $type;
                    $img_link->id_type = $type_id;
                    $img_link->id_image = $img_new->id;
                    $img_link->save();
                }
                $transaction->commit();
            }catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            } catch (\Throwable $e) {
                $transaction->rollBack();
                throw $e;
            }

        }


    }


    public function make_file(){

        $img = $this->img_link->img_r;
        $img_src = Yii::$app->params['path_env'] . 'web/'. $img->name_image;
        $pathinfo = pathinfo($img_src);


        $this->new_file_name =  $pathinfo['filename'] . '.webp';
        $this->new_name_image =  'uploads/' . $this->img_link->type .'/'.$this->img_link->id_type.'/' .$this->new_file_name;


        $this->new_src = Yii::$app->params['path_env'].'web/'. $this->new_name_image;



        $res = $this->app_convert->convert_webp($img_src,$this->new_src);



        if ($res === null ){
            $this->error = true;
        }

    }


    public function create_webp_db_record()
    {

        $orig = $this->img_link->img_r->getOriginal();

        if ($orig == null){
            return;
        }

        $img = new Img();
        $img->setAttributes($this->img_link->img_r->getAttributes());
        $img->webp = 1;
        $img->name_image = $this->new_name_image;
        $img->filename = $this->new_file_name;
        $img->parent_id = $orig->id;

        if ($img->save()) {

            $img_l = new ImgLinks();
            $img_l->id_image = $img->id;
            $img_l->type = $this->img_link->type;
            $img_l->id_type =  $this->img_link->id_type;
            if (! $img_l->save()){
                ex($img_l->getErrors());
            }
        } else {
            ex( $img->getErrors() );
        }
    }

}