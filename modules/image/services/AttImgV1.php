<?php
namespace app\modules\image\services;


use app\modules\image\models\Img;
use app\modules\image\models\ImgLinks;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;
use yii\helpers\BaseFileHelper;
use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\imagine\Image;
/**
 * Created by PhpStorm.
 * User: o.trushkov
 * Date: 24.04.17
 * Time: 11:07
 */
class AttImgV1
{




    private $origImg = null;
    private $childImg = null;

    public $options = [
      /*  'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
        'jpeg_quality' => 100,
        'png_compression_level'=>9,*/
        'resampling-filter' => ImageInterface::FILTER_SINC,
        'flatten' => false

    ];

   // public $filterResize = ImageInterface::FILTER_LANCZOS;
    public $filterResize = ImageInterface::FILTER_UNDEFINED;

    public function preseachImgNew($type,$type_id){

        $this->saveNewImg($type,$type_id);

        if ( Yii::$app->request->post('Img') ){
            $imgs = Yii::$app->request->post('Img');



            foreach ($imgs as  $img_id => $img_form){

                $img = Img::findOne((int)$img_id);
                if ($img !== null){

                    $img->alt = $img_form['alt'];
                    $img->title = $img_form['title'];
                    $img->watermark = (isset($img_form['watermark'])) ? 1: 0 ;
                    $img->update_img =  (isset($img_form['update_img'])) ? 1: 0 ;
                    $img->resize = (isset( $img_form['resize'] )) ? 1: 0 ;
                    $img->harshness = (isset( $img_form['harshness'] )) ? 1: 0 ;


                    $img->width = (int) $img_form['width'];
                    $img->height = (int)$img_form['height'];

                    $img->crop_x = (int)$img_form['crop_x'];
                    $img->crop_y = (int)$img_form['crop_y'];

                    $img->crop_width = (int)$img_form['crop_width'];
                    $img->crop_height = (int)$img_form['crop_height'];

                    $img->wrap_width = (int) $img_form['wrap_width'];
                    $img->wrap_height = (int) $img_form['wrap_height'];

                    //$img->size = $img_form['size'];


                    if ( ! $img->update()){
                        $img->errors = $img->getErrors();
                    }

                    if ($img->update_img && file_exists( $_FILES['Img']['tmp_name'][$img->id]['file'] )){
                        $this->updateFileImg( $img ,$type,$type_id);
                    }
                }
            }
        }
    }

    public function updateFileImg($img,$type,$type_id)
    {


        if ( isset($_FILES['Img']) && isset($_FILES['Img']['name'][$img->id]) ){

            //del old img
            if (file_exists($img->name_image)){
                unlink($img->name_image);
            }

            //get safe name
            $fn = $this->safeImgName( $_FILES['Img']['name'][$img->id]['file'] );
            $img->filename = $fn;
            $img->name_image = 'uploads/' . $type . '/' . $type_id;

            $this->updateOriginal($img);

            /* if ($fn && is_file($img->name_image. '/' . $fn) &&  file_exists($img->name_image. '/' . $fn)) {
                 unlink($img->name_image. '/' . $fn);
             }*/

            $this->resizeImg($img->parent_r,$img,$type,$type_id);
            /*  echo  '<pre>';
              var_dump($img) ;exit;*/
            //  $this->saveAndResize($img,$type,$type_id);
        }

    }

    private function saveNewImg($type,$type_id){

        /*echo '<pre>';
         var_dump( Yii::$app->request->post('Imgnew') ,$_FILES );
         exit;*/

        if (Yii::$app->request->post('Imgnew') && isset($_FILES['Imgnew'])){
            $imgs = Yii::$app->request->post('Imgnew');
            //ex($imgs);
            $files = (isset($_FILES['Imgnew']['name'])) ? $_FILES['Imgnew']['name'] : [];


            foreach ($files as $num => $file_name){

                //if ((int)$imgs[$num]['img_0']['valid'] == 1){

                $imgOrg = $this->saveOriginalNew($num,$type,$type_id);


                //mine list size Image
                // create&set model
                // resize img

                if ( $imgOrg != null ){

                    $img = new Img();
                    $img->alt = $imgs[$num]['alt'];
                    $img->title = $imgs[$num]['title'];
                    $img->width = (int)$imgs[$num]['width'];
                    $img->height = (int)$imgs[$num]['height'];
                    $img->size = $imgs[$num]['size'];
                    $img->watermark = (isset($imgs[$num]['watermark'])) ? (int)$imgs[$num]['watermark'] : 0;
                    $img->resize =(isset($imgs[$num]['resize'])) ? (int)$imgs[$num]['resize'] : 0;
                    $img->harshness =(isset($imgs[$num]['harshness'])) ? (int)$imgs[$num]['harshness'] : 0;



                    $img->crop_x = (int)$imgs[$num]['crop_x'];
                    $img->crop_y = (int)$imgs[$num]['crop_y'];

                    $img->crop_width = (int)$imgs[$num]['crop_width'];
                    $img->crop_height = (int)$imgs[$num]['crop_height'];

                    $img->wrap_width = (int)$imgs[$num]['wrap_width'];
                    $img->wrap_height = (int)$imgs[$num]['wrap_height'];

                   // $img->ord = (int) $imgs[$num]['ord'];


                    if ($img->resize)
                        if ( (! $img->width || ! $img->height) ){
                            $size =explode('_', $img->size);
                            if (count( $size )){
                                $img->width = (int)$size[0];
                                $img->height = (int)$size[1];
                            }

                        }

                    $this->resizeImg($imgOrg,$img,$type,$type_id);
                }
                //   }

            }
        }

        $this->saveImgLinks($type_id,$type);
    }

    public function saveOriginalNew($id,$type,$type_id){

        $img = new Img();
        $img->parent_id = 0;
        $fb = new BaseFileHelper();
        $pathF = 'uploads/'.$type.'/';
        $files = $_FILES['Imgnew'];

        if ( ! is_dir($pathF . $type_id)){ // new dir

            $successCreateFolder = $fb->createDirectory(  $pathF . $type_id,02770 );

            if ($successCreateFolder) {

                if ($files['error'][$id]['file'] == UPLOAD_ERR_OK) {

                    $fn = $this->safeImgName($files['name'][$id]['file']);
                    $img->filename = $fn;

                    move_uploaded_file(
                        $files['tmp_name'][$id]['file'],
                        $pathF . $type_id . '/' . $fn
                    );
                    chmod($pathF . $type_id . '/' . $fn,0660);

                    $img->name_image =  $pathF . $type_id . '/' . $fn;

                }
            }

        } else {
//update
            $files = $_FILES['Imgnew'];

            if ($files ['error'][$id]['file'] == UPLOAD_ERR_OK) {

                $fn = $this->safeImgName($files['name'][$id]['file']); // $files['name'][$id];
                $img->filename = $fn;

                if (file_exists($pathF . $type_id. '/' . $fn)){ //если есть с таким именем уже //добавляем случ имя
                    $ext = explode(".", $fn);
                    if ( count($ext) > 1 ){
                        $newName = $ext[0] .'_' . time() . '_' . rand(100,999) .'.' . $ext[1];
                        move_uploaded_file(
                            $files['tmp_name'][$id]['file'],
                            $pathF . $type_id. '/' .  $newName
                        );
                        chmod($pathF . $type_id. '/' . $newName, 0660);
                        $img->name_image =  $pathF . $type_id . '/' . $newName;
                        $img->filename =  $newName;
                    }
                } else { //new file

                    $fn = $this->safeImgName($files['name'][$id]['file']);

                    move_uploaded_file(
                        $files['tmp_name'][$id]['file'],
                        $pathF . $type_id. '/' . $fn
                    );
                    chmod($pathF . $type_id. '/' . $fn, 0660);
                    $img->name_image =  $pathF . $type_id . '/' . $fn;
                }
            }
            //end foreach
        }

        if ($img->save()){
            //$this->listModel[0][] = $img;
            $this->origImg = $img;
            return $img;
        } else  {
             return null;

        }


    }

    public  function resizeImg($imgOrig,$model,$type,$type_id){
        

        $model->parent_id = $imgOrig->id;
        $dir = 'uploads/'.$type.'/'. $type_id;
        $ext = explode('.',$imgOrig->filename);
        $model->filename = $imgOrig->filename;



        $img =  Image::getImagine()->open(  $imgOrig->name_image );
        $size = $img->getSize();

        $w_src = $size->getWidth();
        $h_src = $size->getHeight();

        if ( $model->resize && $model->crop_height && $model->crop_width ){
            $tmp_name = $dir .'/' .  $ext[0] .  '_temp.'  . $ext[1];



            $model->crop_width = floor($model->crop_width *  ( $w_src / $model->wrap_width ) );
            $model->crop_height = floor( $model->crop_height *  ( $h_src / $model->wrap_height ) );

            $model->crop_x = floor($model->crop_x *  ( $w_src / $model->wrap_width ) );
            $model->crop_y = floor( $model->crop_y *  ( $h_src / $model->wrap_height ) );

            $model->crop_x = abs($model->crop_x);
            $model->crop_y = abs($model->crop_y);
            $crPoint = new Point($model->crop_x,$model->crop_y );
            $crBox = new Box($model->crop_width,$model->crop_height);

            $img->crop($crPoint,$crBox);
            if ($model->harshness){
                $img->effects()->sharpen();
            }

            $img->save($tmp_name,$this->options);

            $w_src = $model->crop_width;
            $h_src = $model->crop_height;
        }


        if ( (! $model->width || ! $model->height) ){
            $size =explode('_', $model->size);
            if (count( $size )){
                $model->width = (int)$size[0];
                $model->height = (int)$size[1];
            }
        }


        if ($model->width == 0 && $model->height == 0){
            $model->width  = $model->crop_width;
            $model->height = $model->crop_height;
        }


        $box_res = new Box( $model->width , $model->height ); // dest size img

        $dest_ratio = $model->width / $model->height ;


///////// horizontal img
        if ( $w_src > $h_src ){

            $new_w = $dest_ratio * $h_src ;
            // center align
            $_D_left_right = ($w_src - $new_w) / 2 ;

            if ($_D_left_right < 0) { // not valid ratio img

                // create new img with white border and past dest img

                $newImg = Image::getImagine()->create(new Box($new_w,$h_src),
                    new \Imagine\Image\Palette\Color\RGB( new \Imagine\Image\Palette\RGB(),[255,255,255], 100 ) );


                // margin  width
                $x_def = abs( floor( ($new_w - $w_src) / 2 ) ); //ceil

                if ($model->watermark){
                    $wm =  Image::getImagine()->open(  'uploads/watermark/watermark.png')->resize(new Box($w_src,$h_src),$this->filterResize);

                    $newImg->paste($img, new Point($x_def,0))->paste($wm, new Point($x_def,0))->resize($box_res,$this->filterResize);
                    if ($model->harshness){
                        $img->effects()->sharpen();
                    }
                    $newImg->save($dir . '/' .  $ext[0] .  '_' . $model->width . '_' . $model->height  . '.' . $ext[1],$this->options);
                } else {

                    $newImg->paste($img, new Point($x_def,0))->resize($box_res,$this->filterResize);
                    if ($model->harshness){
                        $img->effects()->sharpen();
                    }
                    $newImg->save($dir . '/' .  $ext[0] .  '_' . $model->width . '_' . $model->height  . '.' . $ext[1],$this->options);
                }

            }
            if ($_D_left_right > 0){ //valid ratio
                // crop setting point with x,y & box with w,h
                $x = $_D_left_right; $y = 0;
                $w = $new_w; $h = $h_src;

                $point = new Point($x,$y);
                $box = new Box($w, $h);

                if ($model->watermark) {
                    $wm = Image::getImagine()->open('uploads/watermark/watermark.png')->resize($box_res,$this->filterResize);

                    $img->crop($point,$box)->resize($box_res,$this->filterResize)->paste($wm,new Point(0,0));
                    if ($model->harshness){
                        $img->effects()->sharpen();
                    }
                    $img->save($dir . '/' .  $ext[0] .  '_' . $model->width . '_' . $model->height  . '.' . $ext[1],$this->options);
                } else {
                    $img->crop($point,$box)->resize($box_res,$this->filterResize);
                    if ($model->harshness){
                        $img->effects()->sharpen();
                    }
                    $img->save($dir . '/' .  $ext[0] .  '_' . $model->width . '_' . $model->height  . '.' . $ext[1],$this->options);
                }

            }

            if ($_D_left_right == 0) { //save raw

                Yii::$app->session->setFlash('success','картинка сохранилась как есть');

                @copy(
                    $imgOrig->name_image  ,
                    $dir . '/' .  $ext[0] .  '_' . $model->width . '_' . $model->height  . '.' . $ext[1]
                );
                @chmod($dir . '/' .  $ext[0] .  '_' . $model->width . '_' . $model->height  . '.' . $ext[1], 0660);


            }
///////// vertical img
        } else {
            $new_h = $w_src / $dest_ratio;

            // center align
            $_D_left_right = ($h_src - $new_h) / 2 ;

            if ($_D_left_right < 0) { // not valid ratio img

                $newImg = Image::getImagine()->create(new Box($w_src,$new_h),
                    new \Imagine\Image\Palette\Color\RGB( new \Imagine\Image\Palette\RGB(),[255,255,255], 100 ) );

                // margin  height
                $y_def = abs( floor( ($new_h - $h_src) / 2 ) );

                if ($model->watermark){
                    $wm =  Image::getImagine()->open(  'uploads/watermark/watermark.png')->resize(new Box($w_src,$h_src),$this->filterResize);

                    $newImg->paste($img, new Point(0,$y_def))->paste($wm, new Point(0,$y_def))->resize($box_res,$this->filterResize);
                    if ($model->harshness){
                        $img->effects()->sharpen();
                    }
                    $newImg->save($dir . '/' .  $ext[0] .  '_' . $model->width . '_' . $model->height  . '.' . $ext[1],$this->options);
                } else {
                /*    echo '<pre>';
                    var_dump(
                        $img->getSize(),
                        $newImg->getSize()
                    ); exit;*/
                    $newImg->paste($img, new Point(0,$y_def))->resize($box_res,$this->filterResize);
                    if ($model->harshness){
                        $img->effects()->sharpen();
                    }
                    $newImg->save($dir . '/' .  $ext[0] .  '_' . $model->width . '_' . $model->height  . '.' . $ext[1],$this->options);
                }

            }
            if ($_D_left_right > 0){ //valid ratio
                // crop setting point with x,y & box with w,h
                $x = 0;$y = $_D_left_right;
                $w = $w_src; $h = $new_h;

                $point = new Point($x,$y);
                $box = new Box($w, $h);

                if ($model->watermark) {
                    $wm = Image::getImagine()->open('uploads/watermark/watermark.png')->resize($box_res,$this->filterResize);

                    $img->crop($point,$box)->resize($box_res,$this->filterResize)->paste($wm,new Point(0,0));
                    if ($model->harshness){
                        $img->effects()->sharpen();
                    }
                    $img->save($dir . '/' .  $ext[0] .  '_' . $model->width . '_' . $model->height  . '.' . $ext[1],$this->options);
                } else {
                    $img->crop($point,$box)->resize($box_res,$this->filterResize);
                    if ($model->harshness){
                        $img->effects()->sharpen();
                    }
                    $img->save($dir . '/' .  $ext[0] .  '_' . $model->width . '_' . $model->height  . '.' . $ext[1],$this->options);
                }
            }

            if ($_D_left_right == 0) { //save raw

                Yii::$app->session->setFlash('success','картинка сохранилась как есть');

                @copy(
                    $imgOrig->name_image  ,
                    $dir . '/' .  $ext[0] .  '_' . $model->width . '_' . $model->height  . '.' . $ext[1]
                );
                @chmod($dir . '/' .  $ext[0] .  '_' . $model->width . '_' . $model->height  . '.' . $ext[1], 0660);


            }

            // crop setting point with x,y & box with w,h

        }


        if ( file_exists($dir . '/' .  $ext[0] .  '_' . $model->width . '_' . $model->height  . '.' . $ext[1]) ) {

            $model->name_image =  $dir . '/' .  $ext[0] .  '_' . $model->width . '_' . $model->height  . '.' . $ext[1];

            if ($model->save()){
                $this->childImg = $model;
                //$this->listModel[1][] = $model;
            }
            chmod($dir . '/' .  $ext[0] .  '_' . $model->width . '_' . $model->height  . '.' . $ext[1], 0660);
        }

        @unlink(  $tmp_name );

        $model->name_image  = $dir . '/' .  $ext[0] .  '_' . $model->width . '_' . $model->height  . '.' . $ext[1];

        $model->update(false,['name_image','filename']);

    }

    private function updateOriginal($img){


        $oldFn = $img->parent_r->name_image;

        $newFn = $img->name_image . '/' . $img->filename;


        if (file_exists($oldFn)) {
            unlink($oldFn);
        }

        if (file_exists($newFn)){ //если есть с таким именем уже //добавляем случ имя
            $ext = explode(".", $img->filename);
            if ( count($ext) > 1 ){
                $newName = $ext[0] .'_' . time() . '_' . rand(100,999) .'.' . $ext[1];
                $img->filename = $newName;
                $newFn = $img->name_image . '/' . $img->filename;
            }
        }

        /*if (file_exists($newFn)){
            unlink($newFn);
        }*/


        move_uploaded_file(
            $_FILES['Img']['tmp_name'][$img->id]['file'],$newFn
        );
        chmod($newFn,0660);
       // exit;
        $img->parent_r->name_image =  $newFn;
        $img->parent_r->filename = $img->filename;
        $img->parent_r->update(false,['name_image','filename']);

        // $_FILES['Img']['tmp_name'][$model->id]['file']
    }

    private function saveImgLinks($type_id,$type){

        //
        if ($this->childImg && $this->origImg){
            //кол-во картинок данной ширины
            $countOld = (new \yii\db\Query())
                ->select('count(*)')
                ->from('img_links')
                ->leftJoin('img','img_links.id_image = img.id')
                ->where(['img_links.type'=>$type,'img_links.id_type'=>$type_id,
                     'img.width' => $this->childImg->width])->scalar(); //'img.parent_id'=>0,
                //->createCommand()->rawSql;//scalar();
            $ord = (int)((int)$countOld + 1);


            //save link orig
            $imgL = new ImgLinks();
            $imgL->id_image  = $this->origImg->id;
            $imgL->type = $type;
            $imgL->id_type = $type_id;
            $imgL->ord = $ord;
            $imgL->save();

            $this->origImg->ord = $ord; $this->origImg->update(false,['ord']);

            //save child (resize)
            $imgL = new ImgLinks();
            $imgL->id_image  = $this->childImg->id;
            $imgL->type = $type;
            $imgL->id_type = $type_id;
            $imgL->ord = $ord;
            $imgL->save();

            $this->childImg->ord = $ord; $this->childImg->update(false,['ord']);
        }


    }

    private function saveImgLinksOld($listModel,$type_id,$type){
        //ImgLinks::deleteAll([ 'type'=>'product','id_type'=>$type_id]);
        $countOld = (new \yii\db\Query())
            ->select('count(*)')
            ->from('img_links')
            ->leftJoin('img','img_links.id_image = img.id')
            ->where(['img_links.type'=>$type,'img_links.id_type'=>$type_id,'img.parent_id'=>0])
            ->scalar();

        $listImgModelWithParents = $listModel[1];
        $listModel = $listModel[0];
        $count = 0;
        $sortAtt = [];
        foreach ($listModel as $num => $model){
            //    if ($model->parent_id !=null){
            if ($model->parent_id == 0){
                $sortAtt[ $model->id ][ ] = $model;
                $imgL = new ImgLinks();
                $imgL->id_image  = $model->id;
                $imgL->type = $type;
                $imgL->id_type = $type_id;
                $imgL->ord = (int)((int)$countOld + (int)$num);
                $imgL->save();
            } else {
                $sortAtt[$model->parent_id][] = $model;
            }
            //   }

        }
        //  return [ var_dump($sortAtt)];

        foreach ( $listImgModelWithParents as $imgs){

            $countNew = array_search($imgs->parent_id,array_keys($sortAtt));  //count( $sortAtt[$imgs->parent_id]);

            $imgL = new ImgLinks();
            $imgL->id_image  = $imgs->id;
            $imgL->type = $type;
            $imgL->id_type = $type_id;
            $imgL->ord = (int)((int)$count + (int)$countNew);

            $imgL->save();

            if ( count($imgL->getErrors()) > 0){
                return [var_dump($imgL->getErrors())];exit;
            }
        }

    }

    public  static function removeFiles($id_type,$type){
        $dir = 'uploads/'.$type.'/' .$id_type;

        if ( is_dir($dir) ){
            $dir = 'uploads/'.$type.'/' .$id_type;
            FileHelper::removeDirectory($dir);
          /*  $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new \RecursiveIteratorIterator($it,
                \RecursiveIteratorIterator::CHILD_FIRST);
            foreach($files as $file) {
                if ($file->isDir()){
                    rmdir($file->getRealPath());
                } else {
                    unlink($file->getRealPath());
                }
            }
            rmdir($dir);*/
        }
    }

    public static function removeReference($id_action,$type){
        $imgL = ImgLinks::find()->where(['id_type'=>$id_action,'type'=>$type])->all();
        foreach ( $imgL as $ilink){
            $img = Img::findOne(['id'=>$ilink->id_image]);
            if ($img !== null) {
                $ilink->delete();
                $img->delete();
            }
        }


    }

    public static function delAllRef($id_type,$type){
        self::removeFiles($id_type,$type);
        self::removeReference($id_type,$type);
    }

    private  function safeImgName($filename){
        $exp = explode('.', $filename);
        if (count($exp ) == 2 ){
            $smth = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode( $exp[0] ));
            return Inflector::slug(  html_entity_decode($smth,null,'UTF-8') ) .'.'. $exp[1];
        }
        return $filename;
    }

    public function optimizeM($src,$to=null,$q=null){

        if ($to === null) $to = $src;
        if (! file_exists($src) || ! file_exists($to)) return  null;



        try {
            $size = null;
            $imageClient = new ImageClinet() ;
            $url = $imageClient->sendImage($src,$q);
            if ($url){
                $imageClient->loadAndUpdate($url,$to);
                $size  = @filesize($to);
            }


            return $size;

        }  catch(\Exception $e) {
            return null;
            ex(['erroOptm',$e]);
        }

        return null;
    }

}