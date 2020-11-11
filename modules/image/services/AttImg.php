<?php

namespace app\modules\image\services;


use app\modules\image\models\Img;
use app\modules\image\models\ImgLinks;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use Yii;
use yii\helpers\BaseFileHelper;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\imagine\Image;

/**
 * Created by PhpStorm.
 * User: o.trushkov
 * Date: 24.04.17
 * Time: 11:07
 */
class AttImg
{


    private $origImg = null;
    private $childImg = null;

    public $tinyPngKey = 'bCgbm0S8vqcR5Gx_1gLe0PsNIDpr_xgo';

    //public $options = [];


    public $options_Def = [
        // 'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
        'jpeg_quality' => 90,
        'png_compression_level' => 9,
        //'resampling-filter' => ImageInterface::FILTER_LANCZOS,
        //'flatten' => false
    ];

    public $options = [
        // 'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
        'jpeg_quality' => 90,
        'png_compression_level' => 9,
        //'resampling-filter' => ImageInterface::FILTER_LANCZOS,
        //'flatten' => false
    ];

    public $options_PIXELSPERINCH = [
        'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
        'jpeg_quality' => 90,
        'png_compression_level' => 9,
        'resampling-filter' => ImageInterface::FILTER_SINC,
        'flatten' => false

    ];
    public $options_Blur = [
        'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
        'jpeg_quality' => 90,
        'png_compression_level' => 9,
        'resampling-filter' => ImageInterface::FILTER_GAUSSIAN,
        'flatten' => false

    ];

    public $filterResize = ImageInterface::FILTER_UNDEFINED;// ImageInterface::FILTER_SINC;

    public function __construct()
    {
        $this->get_tinypng_key();
    }

    private function get_tinypng_key()
    {

        $keys = ['22l3bY4x0S4VY54TmxFyQ1ZXTr9sYsGL', 'z6JKzrnG48PnjHzsW0FvngnnJZhYkP7L',
            'gPt9lVBh8gcn3TKvLglphT3YlnSlSBGn', 'jQl0svgnHKB6M4fkRVhHJDlWDJy1L0qH',
            'SXd4pZGhDB3Lqb1PnPRCbPfgqGN4Mr2C', 'bVQbzLLBs83cBJz6x5HDKnZp9WjGc272',
            'djSkhnjT2YFPY6wShJmy1TQzTcvZzgNH', 'CCq8zTbShCmR95CM6HDjHrbplTyYxQ20'
        ];
        foreach ($keys as $key) {
            try {
                \Tinify\setKey($key);
                \Tinify\validate();
                $this->tiny_png_key = $key;

                return $this->tiny_png_key;
            } catch (\Tinify\Exception $e) {
                // Validation of API key failed.
            }
        }
        return $this->tiny_png_key;
    }


    public function preseachImgNew($type, $type_id)
    { //main

        $this->saveNewImg($type, $type_id); // add new

        if (Yii::$app->request->post('Img')) { // update old

            $imgs = Yii::$app->request->post('Img');


            foreach ($imgs as $img_id => $img_form) {

                $img = Img::findOne((int)$img_id);

                if ($img !== null) {
                    $oldWmPos = $img->watermark;
                    $oldLogoEnable = $img->logo_r_b;
                    $oldOptimizeEnable = $img->optimize;
                    $updateFile = false;

                    $img->alt = $img_form['alt'];
                    $img->title = $img_form['title'];
                    $img->watermark = (isset($img_form['watermark'])) ? 1 : 0;
                    $img->logo_r_b = (isset($img_form['logo_r_b'])) ? 1 : 0;
                    $img->optimize = (isset($img_form['optimize'])) ? 1 : 0;
                    $img->harshness = (isset($img_form['harshness'])) ? 1 : 0;
                    $img->blur = (isset($img_form['blur'])) ? 1 : 0;

                    $img->update_img = (isset($img_form['update_img'])) ? 1 : 0;
                    $img->resize = (isset($img_form['resize'])) ? 1 : 0;
                    $img->restore = (int)((boolean)$img->watermark == false && (boolean)$oldWmPos == true);


                    $img->width = (int)$img_form['width'];
                    $img->height = (int)$img_form['height'];

                    $img->crop_x = (int)$img_form['crop_x'];
                    $img->crop_y = (int)$img_form['crop_y'];

                    $img->crop_width = (int)$img_form['crop_width'];
                    $img->crop_height = (int)$img_form['crop_height'];

                    $img->wrap_width = (int)$img_form['wrap_width'];
                    $img->wrap_height = (int)$img_form['wrap_height'];


                    if (!$img->update()) {
                        $img->errors = $img->getErrors();
                    }


                    if ($img->update_img && file_exists($_FILES['Img']['tmp_name'][$img->id]['file'])) {

                        if ($img->harshness) {
                            $this->setOptionsHarshness();
                            $this->updateFileImg($img, $type, $type_id);
                            $this->setOptionsDef();
                        } elseif ($img->blur) {
                            $this->setOptionsBlur();
                            $this->updateFileImg($img, $type, $type_id);
                            $this->setOptionsDef();

                        } else {
                            $this->updateFileImg($img, $type, $type_id);
                        }


                        $updateFile = true;
                    }


                    if (($img->watermark && $oldWmPos == false) || ($updateFile && $img->watermark)) {
                        $this->addWatermark($img);
                    }

                    if ($img->logo_r_b && $oldLogoEnable == false) {
                        $this->addLogo($img);
                    }


                    if ($img->restore) {
                        $this->restoreFullSizeItem($img);
                    }

                    if (($img->optimize && $oldOptimizeEnable == false) || ($updateFile && $img->optimize)) {

                        $fs = $img->getFullSizeItem();

                        if ($fs !== null) {

                            $oldSizeOrig = @filesize($fs->name_image);
                            $sizeOrig = $this->optimizeImg($fs->name_image);


                            $oldSizeCrop = @filesize($img->name_image);
                            $sizeCrop = $this->optimizeImg($img->name_image);


                            if ($sizeOrig || $sizeCrop) {
                                $log = 'FullSize ' . round((((100 * $sizeOrig) / $oldSizeOrig)), 2) . '% ' . round($sizeOrig / 1024) . ' кБ ';
                                $log .= 'CropSize ' . round((((100 * $sizeCrop) / $oldSizeCrop)), 2) . '% ' . round($sizeCrop / 1024) . ' кБ ';
                                Yii::$app->session->setFlash('success', $log);

                            }

                        }


                    }


                    $old_webp = $img->getWebPItem();
                    if (is_object($old_webp) && $old_webp->webp && $updateFile) {
                        $this->convertWebPUpdate($old_webp, $img, $type, $type_id);
                    }

                }

            }

        }

    }

    private function saveNewImg($type, $type_id)
    { //add new img


        if (Yii::$app->request->post('Imgnew') && isset($_FILES['Imgnew'])) {
            $imgs = Yii::$app->request->post('Imgnew');
            $files = (isset($_FILES['Imgnew']['name'])) ? $_FILES['Imgnew']['name'] : [];

            foreach ($files as $num => $file_name) {


                $imgOrg = $this->saveOriginal($num, $type, $type_id); //save file & db

                //$imgOrg  as FullBigSize

                //mine list size Image
                // create&set model
                // resize img


                if ($imgOrg !== null) {

                    $img = new Img();
                    $img->alt = $imgs[$num]['alt'];
                    $img->title = $imgs[$num]['title'];
                    $img->size = $imgs[$num]['size'];

                    $img->width = (int)$imgs[$num]['width'];
                    $img->height = (int)$imgs[$num]['height'];


                    $img->watermark = (isset($imgs[$num]['watermark'])) ? (int)$imgs[$num]['watermark'] : 0;
                    $img->resize = (isset($imgs[$num]['resize'])) ? (int)$imgs[$num]['resize'] : 0;
                    $img->optimize = (isset($imgs[$num]['optimize'])) ? (int)$imgs[$num]['optimize'] : 0;
                    $img->logo_r_b = (isset($imgs[$num]['logo_r_b'])) ? (int)$imgs[$num]['logo_r_b'] : 0;
                    $img->harshness = (isset($imgs[$num]['harshness'])) ? (int)$imgs[$num]['harshness'] : 0;
                    $img->blur = (isset($imgs[$num]['blur'])) ? (int)$imgs[$num]['blur'] : 0;


                    $img->webp = (isset($imgs[$num]['webp'])) ? (int)$imgs[$num]['webp'] : 0;


                    $img->crop_x = (int)$imgs[$num]['crop_x'];
                    $img->crop_y = (int)$imgs[$num]['crop_y'];

                    $img->crop_width = (int)$imgs[$num]['crop_width'];
                    $img->crop_height = (int)$imgs[$num]['crop_height'];

                    $img->wrap_width = (int)$imgs[$num]['wrap_width'];
                    $img->wrap_height = (int)$imgs[$num]['wrap_height'];


                    if ($img->resize && (!$img->width || !$img->height)) {
                        $size = explode('_', $img->size);
                        if (count($size)) {
                            $img->width = (int)$size[0];
                            $img->height = (int)$size[1];
                        }
                    }

                    if ($img->blur || $img->harshness) {

                        if ($img->blur) {
                            $this->setOptionsBlur();
                            $this->resizeImg($imgOrg, $img, $type, $type_id);
                            $this->setOptionsDef();

                        }


                        if ($img->harshness) {
                            $this->setOptionsHarshness();
                            $this->resizeImg($imgOrg, $img, $type, $type_id);
                            $this->setOptionsDef();
                        }

                    } else {
                        $this->resizeImg($imgOrg, $img, $type, $type_id);
                    }


                    if ($img->logo_r_b) {
                        $this->addLogo($img);
                    }

                }

            }
        }


        $this->saveImgLinks($type_id, $type);


        if ($this->childImg && $this->origImg) {

            $this->makeOriginal($this->childImg); // $this->childImg => fullSize


            if ($this->childImg->watermark) {

                $this->addWatermark($this->childImg);

            }

            if ($this->childImg->optimize) {

                $oldSizeOrig = @filesize($this->origImg->name_image);
                $sizeOrig = $this->optimizeImg($this->origImg->name_image);


                $oldSizeCrop = @filesize($this->childImg->name_image);
                $sizeCrop = $this->optimizeImg($this->childImg->name_image);


                if ($sizeOrig || $sizeCrop) {
                    $log = 'FullSize ' . round((((100 * $sizeOrig) / $oldSizeOrig)), 2) . '% ' . round($sizeOrig / 1024) . ' кБ ';
                    $log .= 'CropSize ' . round((((100 * $sizeCrop) / $oldSizeCrop)), 2) . '% ' . round($sizeCrop / 1024) . ' кБ ';
                    Yii::$app->session->setFlash('success', $log);

                }

            }

            if ($this->childImg->webp) {
                $this->convertWebP($this->childImg, $type, $type_id);
            }


        }


    }

    public function convertWebP($img, $type, $type_id)
    {
        Yii::warning('wbp convert');

        $imgF = $img;

        if ($imgF === null) {
            var_dump('not find full size');
            exit;
            return;
        }

        $file = ['name' => $imgF->filename,
            'content' => file_get_contents($imgF->name_image)];

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
        $webp_link = '';
        foreach ($imgLinkWebp['urls'] as $url_res) {
            if (strpos($url_res['link'], '.webp') !== false) {
                $webp_link = $url_res['link'];
            }
        }

        if ($webp_link) {
            file_put_contents($file_path, file_get_contents($webp_link));
            chmod($file_path, 0660);

            $img_new->filename = $filename;
            $img_new->name_image = $file_path;

            if ($img_new->save()) {

                $img->webp = 0;
                $img->update(false, ['webp']);


                $img_link = new ImgLinks();
                $img_link->type = $type;
                $img_link->id_type = $type_id;
                $img_link->id_image = $img_new->id;
                $img_link->save();
            }
        }


    }

    public function convertWebPUpdate($old_img_webp, $img, $type, $type_id)
    {

        $imgF = $img;

        if ($imgF === null) {
            var_dump('not find full size');
            exit;
            return;
        }

        $file = ['name' => $imgF->filename,
            'content' => file_get_contents($imgF->name_image)];

        $appOpt = new ImageCompressor([$file]);
        $appOpt->setting['web_b_convert'] = 1;
        $appOpt->optimize_photo();

        $imgLinkWebp = $appOpt->getResult();

        $img_new = $old_img_webp;


        $pathinfo = pathinfo($img->filename);
        $filename = $pathinfo['filename'] . '.webp';
        $dir = 'uploads/' . $type . '/' . $type_id;


        $file_path = $dir . '/' . $filename;
        $webp_link = '';
        foreach ($imgLinkWebp['urls'] as $url_res) {
            if (strpos($url_res['link'], '.webp') !== false) {
                $webp_link = $url_res['link'];
            }
        }

        if ($webp_link) {
            file_put_contents($file_path, file_get_contents($webp_link));

            try {
                chmod($file_path, 0660);
            } catch (\Exception $e) {
                ex([$e->getMessage(), $file_path]);
            }


            $img_new->filename = $filename;
            $img_new->name_image = $file_path;

            if ($img_new->save()) {

            } else {
                ex($img_new->getErrors());
            }
        }


    }

    private function setOptionsHarshness()
    {
        $this->options = $this->options_PIXELSPERINCH;

        $this->filterResize = ImageInterface::FILTER_SINC;
    }

    private function setOptionsBlur()
    {

        $this->options = $this->options_Blur;

        $this->filterResize = ImageInterface::FILTER_GAUSSIAN;
    }


    private function setOptionsDef()
    {
        $this->options = $this->options_Def;

        $this->filterResize = ImageInterface::FILTER_UNDEFINED;
    }

    public function restoreFullSizeItem($img)
    {

        $imgA = $img;

        $orig = $imgA->getOriginal();

        $fs = $imgA->getFullSizeItem();


        if ($orig === null || $fs === null) {
            return -4;
        }


        if (file_exists($fs->name_image)) {
            @unlink($fs->name_image);
        }

        if (file_exists($orig->name_image)) {
            @copy($orig->name_image, $fs->name_image);
            if (file_exists($fs->name_image))
                chmod($fs->name_image, 0660);
        }

    }

    public function addLogo($img)
    {

        $imgF = $img;

        if ($imgF === null) {
            var_dump('not find full size');
            exit;
            return;
        }


        $imgI = Image::getImagine()->open($imgF->name_image);
        $size = $imgI->getSize();

        $w_src = $size->getWidth();
        $h_src = $size->getHeight();

        $imgWm = Img::getImgMain(2530, ImgLinks::T_Wm, 0);
        $wmpath = 'uploads/watermark/text_logo.png';
        if ($imgWm !== null) {
            $wmpath = $imgWm->img_r->name_image;
        }


        $imgWL = Image::getImagine()->open($wmpath);
        $sizeWL = $imgWL->getSize();

        $wl_src = $sizeWL->getWidth();
        $hl_src = $sizeWL->getHeight();

        $k = 0.191;
        if ($w_src / $h_src > 3) {
            $k = $k / 2;
        }

        $ar = ($wl_src) / ($w_src * $k);

        $nw = floor($w_src * $k);
        $nh = floor($hl_src * (1 / $ar));

        $imgWL->resize(new Box(
            $nw,
            $nh
        ), $this->filterResize);


        $px = floor(($w_src) - ($nw + 10));
        $py = floor(($h_src) - ($nh + 10));


        try {

            $imgI->paste($imgWL, new Point($px, $py));
            $imgI->save(null, $this->options);
            return 1;
        } catch (\Exception $e) {
            echo '<pre>';

            var_dump($px, $py);
            var_dump($wl_src, $hl_src);
            var_dump($w_src, $h_src);
            exit;
        }

    }

    public function addWatermark($img)
    {


        $imgA = $img;

        $orig = $imgA->getOriginal();


        if ($orig === null) {
            $imgA = $this->makeOriginal($img);

            if (!(is_object($imgA) && $imgA !== null && !count($imgA->getErrors()))) {
                echo '<pre>';
                //   var_dump($imgA->getErrors());
                var_dump($imgA);
                var_dump('not make orig');
                exit;
                return;
            }
        }


        $imgF = $imgA->getFullSizeItem();

        if ($imgF === null) {
            var_dump('not find full size');
            exit;
            return;
        }


        $imgI = Image::getImagine()->open($imgF->name_image);
        $size = $imgI->getSize();

        $w_src = $size->getWidth();
        $h_src = $size->getHeight();
        $isHor = (boolean)($w_src > $h_src);


        // 2545 × 374
        $imgWm = Img::getImgMain(2545, ImgLinks::T_Wm, 0);
        $wmpath = 'uploads/watermark/text.png';
        if ($imgWm !== null) {
            $wmpath = $imgWm->img_r->name_image;
        }


        $imgW = Image::getImagine()->open($wmpath);
        $sizeW = $imgW->getSize();

        $ww_src = $sizeW->getWidth();
        $hw_src = $sizeW->getHeight();

        $k = 0.618;
        if ($w_src / $h_src > 3) {
            $k = $k / 2;
        }


        $ar = ($ww_src) / ($w_src * $k);


        $nw = floor($w_src * $k);
        $nh = floor($hw_src * (1 / $ar));
        $imgW->resize(new Box(
            $nw,
            $nh
        ), $this->filterResize);

        $px = floor(($w_src / 2) - $nw / 2);
        $py = floor(($h_src / 2) - $nh / 2);

        $imgI->paste($imgW, new Point($px, $py));


        $imgI->save(null, $this->options);

        $this->addLogo($imgF);


    }

    private function makeOriginal($img)
    {
        //uploads/product/38/samogonnyj-apparat-evrostal-2.jpg
        //originals/
        $fb = new BaseFileHelper();
        $parent = $img->parent_r;
        $imgA = $img; // old original
        $imgLA = $img->imgLink_r;
        if (is_object($parent)) {
            $imgA = $parent;
            $imgLA = $parent->imgLink_r;
        }
        $dir = 'uploads/originals/' . $imgLA->type . '/' . $imgLA->id_type;
        $np = 'uploads/originals/' . $imgLA->type . '/' . $imgLA->id_type . '/' . $imgA->filename;
        if (!is_dir($dir)) { // new dir

            $successCreateFolder = $fb->createDirectory($dir, 02770);
            if (!$successCreateFolder) {
                return -2;
            }
        }

        //  ex($imgA);

        if (!file_exists($imgA->name_image)) return -2;
        @copy($imgA->name_image, $np);
        $resc = file_exists($np);
        if (!$resc) {
            return -3;
        } else {
            chmod($np, 0660);
        }


        $imgO = clone $imgA;
        $imgO->id = null;
        $imgO->isNewRecord = true;

        $imgO->original = 1;
        $imgO->name_image = $np;


        if ($imgO->save()) {
            $imgA->parent_id = $imgO->id;
            $imgA->fullsize = 1;
            $imgA->update(false, ['parent_id', 'fullsize']);
            $img->parent_id = $imgO->id;
            $img->update(false, ['parent_id']);
            $imgLN = new ImgLinks();
            $imgLN->id_image = $imgO->id;
            $imgLN->type = $imgLA->type;
            $imgLN->id_type = $imgLA->id_type;
            if ($imgLN->save()) {
                return $imgO;
            }
            return -6;
        } else {
            return -5;
        }

    }

    public function updateFileImg($img, $type, $type_id)
    {


        if (isset($_FILES['Img']) && isset($_FILES['Img']['name'][$img->id])) {
            $big_img = $img->getFullSizeItem();
            if ($big_img) {

                if (file_exists($big_img->name_image)) {
                    unlink($big_img->name_image);
                }

                if (file_exists($img->name_image)) {
                    unlink($img->name_image);
                }


                $fn = $this->safeImgName($_FILES['Img']['name'][$img->id]['file']);


                $img->filename = $fn;
                $img->name_image = 'uploads/originals/' . $type . '/' . $type_id;
                ////////
                $oldFn = $img->parent_r->name_image;

                $newFn = $img->name_image . '/' . $img->filename;


                if (file_exists($oldFn)) {
                    unlink($oldFn);
                }

                if (file_exists($newFn)) { //если есть с таким именем уже //добавляем случ имя
                    $ext = explode(".", $img->filename);
                    if (count($ext) > 1) {
                        $newName = $ext[0] . '_' . time() . '_' . rand(100, 999) . '.' . $ext[1];
                        $img->filename = $newName;
                        $newFn = $img->name_image . '/' . $img->filename;
                    }
                }

                if (FALSE === is_dir('uploads/originals/' . $type . '/' . $type_id)) {
                    $fb = BaseFileHelper::createDirectory('uploads/originals/' . $type . '/' . $type_id,
                        02770);
                }


                move_uploaded_file($_FILES['Img']['tmp_name'][$img->id]['file'], $newFn);
                chmod($newFn, 0660);
                // exit;
                $img->parent_r->name_image = $newFn;
                $img->parent_r->filename = $img->filename;
                $img->parent_r->update(false, ['name_image', 'filename']);

                @copy($newFn, 'uploads/' . $type . '/' . $type_id . '/' . $fn);
                chmod('uploads/' . $type . '/' . $type_id . '/' . $fn, 0660);
                $big_img->name_image = 'uploads/' . $type . '/' . $type_id . '/' . $fn;
                $big_img->filename = $fn;
                $big_img->update(false, ['name_image', 'filename']);


                ////////
                $this->resizeImg($img->parent_r, $img, $type, $type_id);

            } else {
                //del old img
                if (file_exists($img->name_image)) {
                    unlink($img->name_image);
                }


                //get safe name
                $fn = $this->safeImgName($_FILES['Img']['name'][$img->id]['file']);
                $img->filename = $fn;
                $img->name_image = 'uploads/' . $type . '/' . $type_id;

                $this->updateOriginal($img);


                $this->resizeImg($img->parent_r, $img, $type, $type_id);
            }


        }

    }

    private function saveOriginal($id, $type, $type_id)
    {

        $img = new Img();
        $img->parent_id = 0;
        $fb = new BaseFileHelper();
        $pathF = 'uploads/' . $type . '/';
        $files = $_FILES['Imgnew'];


        if (!is_dir($pathF . $type_id)) { // new dir

            $successCreateFolder = $fb->createDirectory($pathF . $type_id, 02770);

            if ($successCreateFolder) {

                if ($files['error'][$id]['file'] == UPLOAD_ERR_OK) {

                    $fn = $this->safeImgName($files['name'][$id]['file']);
                    $img->filename = $fn;

                    move_uploaded_file(
                        $files['tmp_name'][$id]['file'],
                        $pathF . $type_id . '/' . $fn
                    );
                    chmod($pathF . $type_id . '/' . $fn, 0660);

                    $img->name_image = $pathF . $type_id . '/' . $fn;

                }
            }

        } else {
//update
            $files = $_FILES['Imgnew'];

            if ($files ['error'][$id]['file'] == UPLOAD_ERR_OK) {

                $fn = $this->safeImgName($files['name'][$id]['file']); // $files['name'][$id];
                $img->filename = $fn;

                if (file_exists($pathF . $type_id . '/' . $fn)) { //если есть с таким именем уже //добавляем случ имя
                    $ext = explode(".", $fn);
                    if (count($ext) > 1) {
                        $newName = $ext[0] . '_' . time() . '_' . rand(100, 999) . '.' . $ext[1];
                        move_uploaded_file(
                            $files['tmp_name'][$id]['file'],
                            $pathF . $type_id . '/' . $newName
                        );
                        chmod($pathF . $type_id . '/' . $newName, 0660);
                        $img->name_image = $pathF . $type_id . '/' . $newName;
                        $img->filename = $newName;
                    }
                } else { //new file

                    $fn = $this->safeImgName($files['name'][$id]['file']);

                    move_uploaded_file(
                        $files['tmp_name'][$id]['file'],
                        $pathF . $type_id . '/' . $fn
                    );
                    chmod($pathF . $type_id . '/' . $fn, 0660);
                    $img->name_image = $pathF . $type_id . '/' . $fn;
                }
            }
            //end foreach
        }

        if ($img->save()) {
            $this->origImg = $img;
            return $img;
        } else {
            return null;
        }


    }

    public function resizeImg($imgOrig, $model, $type, $type_id, $calcCrop = true)
    {


        $model->parent_id = $imgOrig->id;
        $dir = 'uploads/' . $type . '/' . $type_id;


        $ext = explode('.', $imgOrig->filename);
        $model->filename = $imgOrig->filename;

        $img = Image::getImagine()->open($imgOrig->name_image);
        $size = $img->getSize();

        $w_src = $size->getWidth();
        $h_src = $size->getHeight();




        if ($model->resize && $model->crop_height && $model->crop_width) {
            $tmp_name = $dir . '/' . $ext[0] . '_temp.' . $ext[1];

            if ($calcCrop) {
                $model->crop_width = floor($model->crop_width * ($w_src / $model->wrap_width));
                $model->crop_height = floor($model->crop_height * ($h_src / $model->wrap_height));

                $model->crop_x = floor($model->crop_x * ($w_src / $model->wrap_width));
                $model->crop_y = floor($model->crop_y * ($h_src / $model->wrap_height));
            }


            $model->crop_x = abs($model->crop_x);
            $model->crop_y = abs($model->crop_y);


            $crPoint = new Point($model->crop_x, $model->crop_y);
            $crBox = new Box($model->crop_width, $model->crop_height);

            $img->crop($crPoint, $crBox)->save($tmp_name, $this->options);

            $w_src = $model->crop_width;
            $h_src = $model->crop_height;

        }


        if ((!$model->width || !$model->height)) {
            $size = explode('_', $model->size);
            if (count($size)) {
                $model->width = (int)$size[0];
                $model->height = (int)$size[1];
            }
        }

        if ($model->width == 0 && $model->height == 0){

            $model->width  = $model->crop_width;
            $model->height = $model->crop_height;
            $img->save($dir . '/' . $ext[0] . '_' . $model->width . '_' . $model->height . '.' . $ext[1], $this->options);
        } else {
            $box_res = new Box($model->width, $model->height); // dest size img

            $dest_ratio = $model->width / $model->height;


///////// horizontal img
            if ($w_src > $h_src) {

                $new_w = $dest_ratio * $h_src;
                // center align
                $_D_left_right = ($w_src - $new_w) / 2;


                if ($_D_left_right == 0 && $w_src == $h_src && $h_src == $new_w) { //square size
                    $_D_left_right = -1;
                }


                if ($_D_left_right < 0) { // not valid ratio img

                    // create new img with white border and past dest img
                    //

                    $newImg = Image::getImagine()->create(new Box($new_w, $h_src),
                        new \Imagine\Image\Palette\Color\RGB(new \Imagine\Image\Palette\RGB(), [255, 255, 255], 100));


                    // margin  width
                    $x_def = abs(floor(($new_w - $w_src) / 2)); //ceil

                    $newImg->paste($img, new Point($x_def, 0))
                        ->resize($box_res, $this->filterResize)
                        ->save($dir . '/' . $ext[0] . '_' . $model->width . '_' . $model->height . '.' . $ext[1], $this->options);


                }
                if ($_D_left_right > 0) { //valid ratio
                    // crop setting point with x,y & box with w,h
                    $x = $_D_left_right;
                    $y = 0;
                    $w = $new_w;
                    $h = $h_src;

                    $point = new Point($x, $y);
                    $box = new Box($w, $h);


                    $img->crop($point, $box)->resize($box_res, $this->filterResize)
                        ->save($dir . '/' . $ext[0] . '_' . $model->width . '_' . $model->height . '.' . $ext[1], $this->options);


                }
                if ($_D_left_right == 0) { //save raw

                    Yii::$app->session->setFlash('success', 'картинка сохранилась как есть');

                    @copy(
                        $imgOrig->name_image,
                        $dir . '/' . $ext[0] . '_' . $model->width . '_' . $model->height . '.' . $ext[1]
                    );
                    @chmod($dir . '/' . $ext[0] . '_' . $model->width . '_' . $model->height . '.' . $ext[1], 0660);


                }
///////// vertical img !!
            } else {


                $new_h = $w_src / $dest_ratio;

                // center align
                $_D_left_right = ($h_src - $new_h) / 2;
//ex([
//    $_D_left_right , $h_src , $new_h ,$w_src , $dest_ratio
//]);

                if ($_D_left_right == 0 && $w_src == $h_src && $h_src == $new_h) { //square size
                    $_D_left_right = -1;
                }

                if ($_D_left_right < 0) { // not valid ratio img

                    $newImg = Image::getImagine()->create(new Box($w_src, $new_h),
                        new \Imagine\Image\Palette\Color\RGB(new \Imagine\Image\Palette\RGB(), [255, 255, 255], 100));

                    // margin  height
                    $y_def = abs(floor(($new_h - $h_src) / 2));

                    $newImg->paste($img, new Point(0, $y_def))
                        ->resize($box_res, $this->filterResize)
                        ->save($dir . '/' . $ext[0] . '_' . $model->width . '_' . $model->height . '.' . $ext[1], $this->options);


                }
                if ($_D_left_right > 0) { //valid ratio
                    // crop setting point with x,y & box with w,h
                    $x = 0;
                    $y = $_D_left_right;
                    $w = $w_src;
                    $h = $new_h;

                    $point = new Point($x, $y);
                    $box = new Box($w, $h);


                    $img->crop($point, $box)->resize($box_res, $this->filterResize)
                        ->save($dir . '/' . $ext[0] . '_' . $model->width . '_' . $model->height . '.' . $ext[1],
                            $this->options);

                }

                if ($_D_left_right == 0) { //save raw

                    Yii::$app->session->setFlash('success', 'картинка сохранилась как есть');

                    @copy(
                        $imgOrig->name_image,
                        $dir . '/' . $ext[0] . '_' . $model->width . '_' . $model->height . '.' . $ext[1]
                    );
                    @chmod($dir . '/' . $ext[0] . '_' . $model->width . '_' . $model->height . '.' . $ext[1], 0660);


                }


            }

        }




        if (file_exists($dir . '/' . $ext[0] . '_' . $model->width . '_' . $model->height . '.' . $ext[1])) {
            //del old img
            file_exists($model->name_image);
            @unlink($model->name_image);

            $model->name_image = $dir . '/' . $ext[0] . '_' . $model->width . '_' . $model->height . '.' . $ext[1];

            if ($model->save()) {
                $this->childImg = $model;
                //$this->listModel[1][] = $model;
            } else {
                ex($model->getErrors());
            }
            chmod($dir . '/' . $ext[0] . '_' . $model->width . '_' . $model->height . '.' . $ext[1], 0660);
        }

        @unlink($tmp_name);

        $model->name_image = $dir . '/' . $ext[0] . '_' . $model->width . '_' . $model->height . '.' . $ext[1];

        $model->update(false, ['name_image', 'filename']);

    }

    private function updateOriginal($img)
    {


        $oldFn = $img->parent_r->name_image;

        $newFn = $img->name_image . '/' . $img->filename;


        if (file_exists($oldFn)) {
            unlink($oldFn);
        }

        if (file_exists($newFn)) { //если есть с таким именем уже //добавляем случ имя
            $ext = explode(".", $img->filename);
            if (count($ext) > 1) {
                $newName = $ext[0] . '_' . time() . '_' . rand(100, 999) . '.' . $ext[1];
                $img->filename = $newName;
                $newFn = $img->name_image . '/' . $img->filename;
            }
        }

        /*if (file_exists($newFn)){
            unlink($newFn);
        }*/


        move_uploaded_file(
            $_FILES['Img']['tmp_name'][$img->id]['file'], $newFn
        );
        chmod($newFn, 0660);
        // exit;
        $img->parent_r->name_image = $newFn;
        $img->parent_r->filename = $img->filename;
        $img->parent_r->update(false, ['name_image', 'filename']);

        // $_FILES['Img']['tmp_name'][$model->id]['file']
    }

    private function saveImgLinks($type_id, $type)
    {

        //
        if ($this->childImg && $this->origImg) {
            //кол-во картинок данной ширины
            $countOld = (new \yii\db\Query())
                ->select('count(*)')
                ->from('img_links')
                ->leftJoin('img', 'img_links.id_image = img.id')
                ->where(['img_links.type' => $type, 'img_links.id_type' => $type_id,
                    'img.width' => $this->childImg->width])->scalar(); //'img.parent_id'=>0,
            //->createCommand()->rawSql;//scalar();
            $ord = (int)((int)$countOld + 1);


            //save link orig
            $imgL = new ImgLinks();
            $imgL->id_image = $this->origImg->id;
            $imgL->type = $type;
            $imgL->id_type = $type_id;
            $imgL->ord = $ord;
            $imgL->save();

            $this->origImg->ord = $ord;
            $this->origImg->update(false, ['ord']);

            //save child (resize)
            $imgL = new ImgLinks();
            $imgL->id_image = $this->childImg->id;
            $imgL->type = $type;
            $imgL->id_type = $type_id;
            $imgL->ord = $ord;
            $imgL->save();

            $this->childImg->ord = $ord;
            $this->childImg->update(false, ['ord']);
        }


    }

    public function optimizeImg($src, $to = null)
    {

        if ($to === null) $to = $src;
        if (!file_exists($src) || !file_exists($to)) return null;

        try {
            \Tinify\setKey($this->tinyPngKey);
            \Tinify\validate();
        } catch (\Tinify\Exception $e) {
            // Validation of API key failed.
            return null;
        }

        try {
            // Use the Tinify API client.
            $source = \Tinify\fromFile($src);
            $size = $source->toFile($to);


            return $size;

        } catch (\Exception $e) {
            // Something else went wrong, unrelated to the Tinify API.
        }

        return null;
    }

    public function optimizeM($src, $to = null, $q = null)
    {

        if ($to === null) $to = $src;
        if (!file_exists($src) || !file_exists($to)) return null;


        try {
            $size = null;
            $imageClient = new ImageClinet();
            $url = $imageClient->sendImage($src, $q);
            if ($url) {
                $imageClient->loadAndUpdate($url, $to);
                $size = @filesize($to);
            }


            return $size;

        } catch (\Exception $e) {
            return null;
            ex(['erroOptm', $e]);
        }

        return null;
    }

    public function convert_webp($src, $to = null)
    {


        if (!file_exists($src)) return null;

        // try {
        $size = null;
        $imageClient = new ImageClinet();
        $url = $imageClient->sendImageConvert($src);


        if ($url) {
            $data = file_get_contents($url);
            ex($url);
            file_put_contents($to, $data);
            chmod($to, 0660);
            $size = @filesize($to);
        }


        return $size;

        //  }  catch(\Exception $e) {
        //    ex(['erroOptm',$e]);
        //      return null;
        //     ex(['erroOptm',$e]);
        //    }


        return null;
    }


    public function cropCalcInfo($imgOrig, $model, $type, $type_id, $newsize)
    {
        $img = Image::getImagine()->open($imgOrig->name_image);

        $size = $img->getSize();

        $w_src = $size->getWidth();
        $h_src = $size->getHeight();

        $newSize = explode('_', $newsize);

        $c_w = $newSize[0];
        $c_h = $newSize[1];

        if ($w_src > $h_src) { // horiz
            $c_ratio = $c_w / $c_h;
            $model->crop_width = floor($h_src * $c_ratio);
            $model->crop_height = $h_src;
            $model->crop_y = 0;
            $model->crop_x = floor(($w_src - $model->crop_width) / 2);

            //$model->crop_x =
        } else { // vert
            $c_ratio = $c_w / $c_h;
            $model->crop_width = $w_src;
            $model->crop_height = floor($h_src / $c_ratio);
            $model->crop_y = floor(($h_src - $model->crop_height) / 2);
            $model->crop_x = 0;

        }


        /*
        $model->crop_height = 1;
        $model->crop_width = 2;*/

        $model->wrap_width = 1;
        $model->wrap_height = 1;
        $model->width = $c_w;
        $model->height = $c_h;
        $model->size = $newSize;
        //$model->update(false,['width','height','name_image','filename']);
    }

    public static function removeFiles($id_type, $type)
    {
        $dir = 'uploads/' . $type . '/' . $id_type;
        if (is_dir($dir)) {
            $dir = 'uploads/' . $type . '/' . $id_type;
            FileHelper::removeDirectory($dir);

        }
    }

    public static function removeReference($id_action, $type)
    {
        $imgL = ImgLinks::find()->where(['id_type' => $id_action, 'type' => $type])->all();
        foreach ($imgL as $ilink) {
            $img = Img::findOne(['id' => $ilink->id_image]);
            if ($img !== null) {
                $ilink->delete();
                $img->delete();
            }
        }


    }

    public static function delAllRef($id_type, $type)
    {
        self::removeFiles($id_type, $type);
        self::removeReference($id_type, $type);
    }

    private function safeImgName($filename)
    {

        $path_parts = pathinfo($filename);

        $filename = $path_parts['filename'];
        $ext = $path_parts['extension'];

        $filename = str_replace('.', '', $filename);
        $smth = preg_replace("/%u([0-9a-f]{3,4})/i", "&#x\\1;", urldecode($filename));
        $filename = Inflector::slug(html_entity_decode($smth, null, 'UTF-8'));

        $safe_name = $filename . '.' . $ext;
        if (file_exists($filename)) {
            $safe_name = $filename . '_' . rand(1000, 9999) . '.' . $ext;
        }

        return $safe_name;

    }

}