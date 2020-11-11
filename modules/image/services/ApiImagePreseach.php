<?php
namespace app\modules\image\services;


use app\modules\image\models\Img;
use app\modules\image\models\ImgLinks;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use Tinify\Tinify;
use yii\base\Security;
use yii\db\Transaction;
use yii\helpers\BaseFileHelper;
use yii\imagine\Image;
use Yii;
use yii\web\BadRequestHttpException;

class ApiImagePreseach {


    public $rawData=[];
    public $options=[];

    public $filterResize = ImageInterface::FILTER_UNDEFINED;

    public $ent;
    public $ent_id;

    public $img;
    public $imglink;


    public $sourceUrl = 'http://spn-group.ru';


    //public $sourceUrl = 'http://seopost.it-06.aim';

    public $folder;
    public $original;

    public $tinyPngKey = 'bCgbm0S8vqcR5Gx_1gLe0PsNIDpr_xgo';

    public $orig;
    public $crop;
    public $fullSize;
    public $name;



    public  function  preseach(){

        $this->img = new Img();

        $this->img->setAttributes($this->rawData);
        if (!$this->rawData['img_src'] ) return;
        $this->img->name_image = $this->rawData['img_src'];

        if (isset($this->rawData['logo_r_b'])){
            $this->img->logo_r_b = $this->rawData['logo_r_b'];
        }
        if (isset($this->rawData['watermark'])){
            $this->img->watermark = $this->rawData['watermark'];
        }




        $this->crDirs();
        $size = $this->downloadImg();

        if ( ! $size ) return $size;


        $transaction = Yii::$app->db->beginTransaction(Transaction::SERIALIZABLE);
        try {


            $this->createOriginal();
            if ($this->img->optimize){
                $this->optimizeImg( $this->folder.'/'. $this->name);
            }

            $this->createCrop();


            $this->createFullSize();


            $transaction->commit();
        }catch (Exception $e) {
            $transaction->rollBack();
            throw new BadRequestHttpException($e->getMessage(), 0, $e);
        }




    }

    private function addLogo(){







        $imgWm =  Image::getImagine()->open( $this->folder . '/'.$this->name );
        $size = $imgWm->getSize();

        $w_src = $size->getWidth();
        $h_src = $size->getHeight();




        $imgWS = Img::getImgMain(2530,ImgLinks::T_Wm, 0 );
        $wmpath = 'uploads/watermark/text_logo.png';
        if ($imgWS !== null ){
            $wmpath = $imgWS->img_r->name_image;
        }

        $imgWL =  Image::getImagine()->open(  $wmpath );

        $sizeWL = $imgWL->getSize();

        $wl_src = $sizeWL->getWidth();
        $hl_src = $sizeWL->getHeight();

        $k = 0.191;
        if ( $w_src / $h_src > 3 ){
            $k = $k / 2;
        }

        $ar = ($wl_src)/($w_src * $k);

        $nw = floor($w_src * $k);
        $nh = floor($hl_src*(1/$ar));

        $imgWL->resize( new Box(
            $nw,
            $nh
        ),$this->filterResize);




        $px = floor( ($w_src  ) - ( $nw + 10 ));
        $py = floor( ($h_src  ) - ($nh + 10));


        try {

            $imgWm->paste($imgWL, new Point($px,$py));
            $imgWm->save(null,$this->options);
            return 1;
        } catch ( \Exception $e){
            echo '<pre>';

            var_dump($px,$py);
            var_dump($wl_src,$hl_src);
            var_dump($w_src,$h_src);
            exit;
        }
    }

    private function addWatermark(){

        $imgI =  Image::getImagine()->open( $this->folder . '/'.$this->name );
        $size = $imgI->getSize();

        $w_src = $size->getWidth();
        $h_src = $size->getHeight();


        // 2545 × 374
        $imgWm = Img::getImgMain(2545,ImgLinks::T_Wm, 0 );
        $wmpath = 'uploads/watermark/text.png';
        if ($imgWm !== null ){
            $wmpath = $imgWm->img_r->name_image;
        }


        $imgW =  Image::getImagine()->open(  $wmpath);
        $sizeW = $imgW->getSize();

        $ww_src = $sizeW->getWidth();
        $hw_src = $sizeW->getHeight();

        $k = 0.618;
        if ( $w_src / $h_src > 3 ){
            $k = $k / 2;
        }


        $ar = ($ww_src)/($w_src * $k);



        $nw = floor($w_src * $k);
        $nh = floor($hw_src*(1/$ar));
        $imgW->resize( new Box(
            $nw,
            $nh
        ),$this->filterResize);

        $px = floor( ($w_src / 2 ) - $nw / 2);
        $py = floor( ($h_src / 2 ) - $nh / 2);

        $imgI->paste($imgW, new Point($px,$py));


        $imgI->save(null,$this->options);

        ////////////




    }

    private function createOriginal(){

        copy($this->folder . '/'.$this->name,
            $this->original. '/'.$this->name);
        @chmod($this->original. '/'.$this->name, 0660);

        $this->orig = new Img();
        $this->orig->setAttributes($this->img->getAttributes());
        $this->orig->original = 1;
        $this->orig->optimize = 0;
        $this->orig->name_image = $this->original . '/'.$this->name;



        if (! $this->orig->save()){
            ex($this->orig->getErrors());
        }

        $this->addLink($this->orig->id);
    }




    private function createCrop(){

        $this->crop = new Img();

        $this->crop->setAttributes($this->img->getAttributes());
        $this->crop->original = 0;

        $this->crop->size = $this->rawData['size'];



        $this->resizeImg($this->orig,$this->crop);

        $this->crop->parent_id = $this->orig->id;
        $this->crop->save();


        if (! $this->crop->save()){
            ex($this->crop->getErrors());
        }

        $this->addLink($this->crop->id);
    }

    private function createFullSize(){

        $this->fullSize = new Img();
        $this->fullSize->setAttributes($this->img->getAttributes());
        $this->fullSize->parent_id = $this->orig->id;
        $this->fullSize->fullsize = 1;
        $this->fullSize->original = 0;
        $this->fullSize->name_image = $this->folder .  '/' .$this->name;

        if ($this->img->watermark){
            $this->addWatermark();
        }

        if ($this->img->logo_r_b){
            $this->addLogo();
        }


        if (! $this->fullSize->save()){
            ex($this->fullSize->getErrors());
        }
        $this->fullSize->save();
        $this->addLink($this->fullSize->id);
    }



    public  function resizeImg($imgOrig,$model){



        $size = explode('_', $model->size);

        if (count( $size )){
            $model->width = (int)$size[0];
            $model->height = (int)$size[1];
        }



        $model->filename = $imgOrig->filename;

        $img =  Image::getImagine()->open(   $this->folder .'/'.$this->name  ); //1
        $size = $img->getSize();

        $w_src = $size->getWidth();
        $h_src = $size->getHeight();


        if ( (int) $model->resize && $model->crop_height && $model->crop_width ){
            // $tmp_name = $dir .'/' .  $ext[0] .  '_temp.'  . $ext[1];

            //    if ( $calcCrop ){
            $model->crop_width = floor($model->crop_width *  ( $w_src / $model->wrap_width ) );
            $model->crop_height = floor( $model->crop_height *  ( $h_src / $model->wrap_height ) );

            $model->crop_x = abs(floor($model->crop_x *  ( $w_src / $model->wrap_width ) ));
            $model->crop_y = abs(floor( $model->crop_y *  ( $h_src / $model->wrap_height ) ));
            //  }

            $crPoint = new Point($model->crop_x,$model->crop_y );
            $crBox = new Box($model->crop_width,$model->crop_height);

            $img->crop($crPoint,$crBox);

            if ((int)$model->harshness){
                $img->effects()->sharpen();
            }

            $img->save($this->folder .'/'.$model->size.'_'.$this->name ,
                $this->options);

            @chmod($this->folder .'/'.$model->size.'_'.$this->name, 0660);


        } else {

            $box_res = new Box( $model->width , $model->height ); // dest size img

            $dest_ratio = $model->width / $model->height;


///////// horizontal img
            if ( $w_src > $h_src ){

                $new_w = $dest_ratio * $h_src ;
                // center align
                $_D_left_right = ($w_src - $new_w) / 2 ;



                if ($_D_left_right < 0) { // not valid ratio img

                    // create new img with white border and past dest img
                    //

                    $newImg = Image::getImagine()->create(new Box($new_w,$h_src),
                        new \Imagine\Image\Palette\Color\RGB( new \Imagine\Image\Palette\RGB(),[255,255,255], 100 ) );


                    // margin  width
                    $x_def = abs( floor( ($new_w - $w_src) / 2 ) ); //ceil

                    $newImg->paste($img, new Point($x_def,0))
                        ->resize($box_res,$this->filterResize);

                    if ((int)$model->harshness){
                        $img->effects()->sharpen();
                    }
                    $img->save($this->folder .'/'.$model->size.'_'.$this->name,$this->options);
                    @chmod($this->folder .'/'.$model->size.'_'.$this->name, 0660);

                }
                if ($_D_left_right > 0) { //valid ratio
                    // crop setting point with x,y & box with w,h
                    $x = $_D_left_right; $y = 0;
                    $w = $new_w; $h = $h_src;

                    $point = new Point($x,$y);
                    $box = new Box($w, $h);



                    $img->crop($point,$box)->resize($box_res,$this->filterResize);
                    if ((int)$model->harshness){
                        $img->effects()->sharpen();
                    }
                    $img->save($this->folder .'/'.$model->size.'_'.$this->name,$this->options);
                    @chmod($this->folder .'/'.$model->size.'_'.$this->name, 0660);

                }
                if ($_D_left_right == 0) { //save raw

                    \Yii::$app->session->setFlash('success','картинка сохранилась как есть');

                    @copy(
                        $imgOrig->name_image  ,
                        $this->folder .'/'.$model->size.'_'.$this->name
                    );
                    @chmod($this->folder .'/'.$model->size.'_'.$this->name, 0660);


                }
///////// vertical img !!
            } else {
                $new_h = $w_src / $dest_ratio;

                // center align
                $_D_left_right = ($h_src - $new_h) / 2 ;

                if ($_D_left_right < 0) { // not valid ratio img

                    $newImg = Image::getImagine()->create(new Box($w_src,$new_h),
                        new \Imagine\Image\Palette\Color\RGB( new \Imagine\Image\Palette\RGB(),[255,255,255], 100 ) );

                    // margin  height
                    $y_def = abs( floor( ($new_h - $h_src) / 2 ) );

                    $newImg->paste($img, new Point(0,$y_def))
                        ->resize($box_res,$this->filterResize);
                    if ((int)$model->harshness){
                        $img->effects()->sharpen();
                    }
                    $img->save($this->folder .'/'.$model->size.'_'.$this->name,$this->options);
                    @chmod($this->folder .'/'.$model->size.'_'.$this->name, 0660);

                }
                if ($_D_left_right > 0) { //valid ratio
                    // crop setting point with x,y & box with w,h
                    $x = 0;$y = $_D_left_right;
                    $w = $w_src; $h = $new_h;

                    $point = new Point($x,$y);
                    $box = new Box($w, $h);


                    $img->crop($point,$box)->resize($box_res,$this->filterResize);
                    if ((int)$model->harshness){
                        $img->effects()->sharpen();
                    }
                    $img->save($this->folder .'/'.$model->size.'_'.$this->name,
                        $this->options);
                    @chmod($this->folder .'/'.$model->size.'_'.$this->name, 0660);

                }

                if ($_D_left_right == 0) { //save raw

                    \Yii::$app->session->setFlash('success','картинка сохранилась как есть');

                    @copy(
                        $imgOrig->name_image  ,
                        $this->save .'_'.$size
                    );
                    @chmod($this->folder .'/'.$model->size.'_'.$this->name, 0660);


                }



            }
        }



        if ( file_exists($this->folder .'/'.$model->size.'_'.$this->name) ) {
            chmod($this->folder .'/'.$model->size.'_'.$this->name, 0660);
        }


        $model->name_image  = $this->folder .'/'.$model->size.'_'.$this->name;


    }

    private function downloadImg(){
        $size = file_put_contents(
            $this->folder . '/'.$this->name,
            @fopen($this->sourceUrl.$this->img->name_image, 'r')
        );
        @chmod($this->folder . '/'.$this->name, 0660);
        return  $size;
    }




    private function crDirs(){
        $fb = new BaseFileHelper();



        if ($this->img->name_image){
            $path_parts = pathinfo($this->img->name_image);

            $ext =  $path_parts['extension']; // explode('.',$this->img->name_image)[1];
            $name = time().rand(1000, 9999) . '.'.$ext;
            $this->name = $name;
            $this->img->filename = $name;

            $this->folder = 'uploads/'.$this->ent.'/'.$this->ent_id;
            $this->original = 'uploads/originals/'.$this->ent.'/'.$this->ent_id;
        }


        if ( ! is_dir( $this->original)){
            $successCreateFolder = $fb->createDirectory(   $this->original,02770 );
        }
        if ( ! is_dir( $this->folder)){
            $successCreateFolder = $fb->createDirectory(   $this->folder,02770 );
        }

    }


    public function optimizeImg($src,$to=null){

        if ($to === null) $to = $src;
        if (! file_exists($src) || ! file_exists($to)) return null;

        try {
            \Tinify\setKey($this->tinyPngKey);
            \Tinify\validate();
        } catch(\Tinify\Exception $e) {
            // Validation of API key failed.
            return null;
        }

        try {
            // Use the Tinify API client.
            $source = \Tinify\fromFile($src);
            $size =  $source->toFile($to);

            //update count uses
            $settings = \Yii::$app->getModule('settings');
            $settings->editVar('tinypng_count', Tinify::getCompressionCount() );

            return $size;

        }  catch(\Exception $e) {
            // Something else went wrong, unrelated to the Tinify API.
        }

        return null;
    }

    private function addLink($id_image){
        $l = new ImgLinks();
        $l->id_type = $this->ent_id;
        $l->type = $this->ent;
        $l->id_image = $id_image;
        $l->save();
    }


    public function __construct()
    {
        $settings = \Yii::$app->getModule('settings');
        $key =  (int)$settings->getVar('tinypng_api_key');

        if ($key){
            $this->tinyPngKey = $key;
        }
    }




}