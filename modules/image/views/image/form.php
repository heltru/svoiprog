<!-- картинки-->
<?php

$this->registerJsFile('/js/jquery.Jcrop.min.js', ['position' => yii\web\View::POS_END]);
$this->registerJsFile('/js/image/ImgUpload.js', ['position' => yii\web\View::POS_END]);


?>
<div class="row" data-role="ImgUpload">

    <input type="hidden" name="image_list">

    <h3>Добавить картинку</h3>

    <div class="col-lg-5 col-xs-12">
        <img class="img-responsive" src="/images/noimage/noname.png" alt="">
    </div>

    <div class="col-lg-3 col-xs-12">

        <div class="row">
            <div class="col-xs-12">
                <input type="file" name="img_file" accept="image/*">
                <span style="display: none" class="raw_caption">
                    размер <span class="raw_size">123 kb</span></span>
            </div>

            <div class="col-xs-6">
                <label class="control-label">Обновить файл</label>
                <input type="checkbox" class="form-control" name="img_update">
            </div>
            <div class="col-xs-6">
                <label class="control-label">Водяной знак</label>
                <input type="checkbox" class="form-control" name="img_watermark">
            </div>
            <div class="col-xs-6">
                <label class="control-label">Resize</label>
                <input type="checkbox" class="form-control" name="img_resize">
            </div>

            <div class="col-xs-6">
                <label class="control-label">Оптимизация</label>
                <input type="checkbox" class="form-control" name="img_optimize">
            </div>
            <div class="col-xs-6">
            </div>
            <div class="col-xs-6">
            </div>


            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-12">
                        <select class="form-control" name="img_size" style="margin-top: 0.5em;">
                            <option value="558_351">558_351 Заглавный</option>
                            <option value="150_120">150_120 Второстепенный</option>
                            <option value="1165_776">1165_776 Второстепенный</option>
                            <option value="1160_503">1160_503 Прямоугольный</option>
                            <option value="914_856">914_856 Квадратный</option>
                            <option value="0_0">Авто</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <input type="hidden" name="img_width">
                    <input type="hidden" name="img_height">
                    <input type="hidden" name="img_crop_x" value="0">
                    <input type="hidden" name="img_crop_y" value="0">
                    <input type="hidden" name="img_crop_width" value="0">
                    <input type="hidden" name="img_crop_height" value="0">
                    <input type="hidden" name="img_wrap_width" value="0">
                    <input type="hidden" name="img_wrap_height" value="0">
                </div>
            </div>
        </div>


    </div>
    <div class="col-lg-4 col-xs-12">
        <div class="row">
            <div class="col-xs-12">
                <label class="control-label">Alt</label>
                <textarea class="form-control imgAlt" name="img_alt"></textarea>
            </div>
            <div class="col-xs-12 ">
                <label class="control-label">Title</label>
                <textarea class="form-control" name="img_title"></textarea>
            </div>
        </div>
    </div>

</div>
<script>
    $(document).ready(function () {

        // var jcrop_api;
        // var img;
        //
        // var conImg = $('.imgPrevUpload');
        //
        // var cont;
        //

    });
</script>