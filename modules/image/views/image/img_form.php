<!-- картинки-->
<?php

use yii\helpers\Url;

$this->registerJsFile(
    '/js/jquery.Jcrop.min.js',
    ['position' => yii\web\View::POS_END]
);

?>
<div class="row images-form">

    <div class="col-xs-12 newImgs" style="margin-bottom: 2em;">
        <h3>Добавить картинку</h3>

        <?php
        $nimg = new \app\modules\image\models\Img();
        $nimg->ord = $dataProviderImgLinks->count;
        echo $this->render('_img', ['model' => $nimg,
            'new' => true,
            'tab_content_header' => $tab_contentHeader]); ?>
    </div>


    <?php
    //$dataProviderImgLinks


    $tab_content = [];
    foreach ($dataProviderImgLinks->getModels() as $img) {

        $key = (string)$img->width . '_' . $img->height;

        if (!isset($tab_content[$key])) $tab_content[$key] = '';

        $tab_content[$key] = $tab_content[$key] .
            $this->render('_img', ['model' => $img, 'new' => false,
                'tab_content_header' => $tab_contentHeader, 'key' => $key]);
    }

    $items = [];
    foreach ($tab_content as $key => $tab_cont) {
        $tab = [
            'label' => (string)(isset($tab_contentHeader[$key])) ? $tab_contentHeader[$key] : $key,
            'content' => $tab_cont,
            'options' => ['id' => 'tab' . $key],

        ];
        $items[] = $tab;
    }
    ?>

    <div class="col-xs-12">
        <?php
        echo \yii\bootstrap\Tabs::widget([
            'items' => $items,
            'encodeLabels' => false,
        ]);

        ?>
    </div>
</div>
<script>
    $(document).ready(function () {

        var jcrop_api;
        var img;

        var conImg = $('.imgPrevUpload');

        var cont;

        $.each(matrSize, function (i, v) {

            $("div#tab" + v).sortable({
                //  items: "div.row ",
                update: function () {
                    var arrId = [];

                    $.each($(this).find('.itemImg'), function (i, v) {

                        arrId.push($(this).attr('idsort'));
                    });

                    $.ajax({
                        type: "POST",
                        url: "<?= Url::to(['/admin/image/default/ajax-img-sort'])?>",
                        data: {_csrfbe: yii.getCsrfToken(), ids: arrId.join(',')},
                        context: document.body
                    });
                },
                placeholder: "ui-state-highlight-group",
                handle: $(".imgSort")

            });
        });

        $('body').on('change', '.imgSize', function (e) {
            //console.log(String( $(this).val()).split('_'));
            var val = String($(this).val()).split('_');
            $(cont).find('.imgWidth').val(val[0]);
            $(cont).find('.imgHeight').val(val[1]);
            //  $(this).parent().parent().parent().find('.imgWidth').val(val[0]);
            //   $(this).parent().parent().parent().find('.imgHeight').val(val[1]);
            var ratio = val[0] / val[1];

            if (jcrop_api) {

                jcrop_api.setOptions(
                    {
                        aspectRatio: ratio
                    });
            }
        });

        $('.images-form').on('click', '.imageDeleteN', function (e) {

            e.preventDefault();

            $(this).parent().parent().parent().parent().parent().remove();
            var id = $(this).data('id');
            if (id) {
                $.ajax({
                    type: "POST",
                    //  async:false,
                    url: "<?= Url::to(['/admin/image/default/ajax-delete'])?>",
                    data: {
                        id: id,
                        _csrfbe: yii.getCsrfToken()
                    }
                });
            }
        });

        $('.images-form').on('click', '.imageDelete', function (e) {

            $(this).parent().parent().parent().parent().parent().remove();
            var id = $(this).data('id');
            if (id) {
                $.ajax({
                    type: "POST",
                    async: false,
                    url: "<?= Url::to(['/img/ajax-delete'])?>" + '/?id=' + id,
                    data: {
                        _csrfbe: yii.getCsrfToken()
                    }
                });
            }
        });

        $('body').on('click', '.btnImgOpt', function (e) {

            if (jcrop_api) {
                jcrop_api.destroy();
                $(img).removeAttr('style');
            }
            var cont = $(this).parent().parent().parent().parent().parent();

            var imgc = cont.find('.imgPrevUpload');
            var img_input = cont.find('.imgFile')[0].files[0];


            var form = new FormData();
            form.append('imgpreview', img_input, img_input.name);
            $.ajax({
                type: "POST",
                url: "/admin/image/default/optimize-ajax",
                data: form,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (typeof data == 'object') {
                        if (data.status == 200) {

                            imgc.attr('src', data.response.src);


                            $('.optimize-caption').hide();
                            cont.find('.btnImgOpt').show();
                            cont.find('.optimize-caption').show();
                            cont.find('.optimize_size').text((data.response.newsize / 1024).toFixed(2) + ' кБ');
                            cont.find('.btnImgOpt').css('color', '#333');

                            $(img).Jcrop({
                                onSelect: showCoords,
                                onChange: changeCoords,
                                boxWidth: 500, boxHeight: 500
                            }, function () {
                                jcrop_api = this;

                                $(cont).find('.imgWrapWidth').val($(img).width());
                                $(cont).find('.imgWrapHeight').val($(img).height());
                                $(cont).find('.imgResize').prop('checked', true);

                                setRatio($(cont).find('.imgSizeNew option:selected').val());
                            });


                        }

                    }

                },
                beforeSend: function (e) {
                    cont.find('.btnImgOpt').css('color', 'red');

                }
            });

        });

        $('body').on('change', '.imgSizeNew', function (e) {

            setRatio($(this).val());
        });

        function setRatio(size) {
            var val = String(size).split('_');

            var ratio = val[0] / val[1];
            if (jcrop_api) {

                jcrop_api.setOptions(
                    {
                        aspectRatio: ratio
                    });
            }
        }

        changeFiles = function (e) {

            $(this).parent().parent().find('.updateImg').prop('checked', true);
            $(this).parent().parent().find('.imgWebp').prop('checked', true);

            /*var img = */
            if (jcrop_api) {
                jcrop_api.destroy();
                $(img).removeAttr('style');
                //   return;
            }
            img = $(this).parent().parent().parent().parent().find('.imgPrevUpload');


            //clearCont();
            var files = e.target.files; // FileList object

            console.log('FILES',files);

            // Loop through the FileList and render image files as thumbnails.
            for (var i = 0, f; f = files[i]; i++) {

                // Only process image files.
                if (!f.type.match('image.*')) {
                    continue;
                }
                var reader = new FileReader();

                // Closure to capture the file information.
                reader.onload = (function (theFile) {

                    return function (e) {

                        $(img).attr('src', e.target.result);

                        cont = $(img).parent().parent();
                        $(cont).find('.btnImgOpt').show();
                        $(cont).find('.raw_caption').show();
                        $(cont).find('.raw_size').text((theFile.size / 1000).toFixed(2) + ' кБ');


                        $(img).Jcrop({
                            onSelect: showCoords,
                            onChange: changeCoords,
                            boxWidth: 500, boxHeight: 500
                        }, function () {
                            jcrop_api = this;

                            $(cont).find('.imgWrapWidth').val($(img).width());
                            $(cont).find('.imgWrapHeight').val($(img).height());
                            $(cont).find('.imgResize').prop('checked', true);

                            setRatio($(cont).find('.imgSizeNew option:selected').val());
                        });
                    }

                })(f);
                reader.readAsDataURL(f);

            }
        };

        function showCoords(c) {

            $(cont).find('.imgCropX').val(Math.floor(c.x));
            $(cont).find('.imgCropY').val(Math.floor(c.y));
            $(cont).find('.imgCropWidth').val(Math.floor(c.w));
            $(cont).find('.imgCropHeight').val(Math.floor(c.h));

        }

        function changeCoords(c) {
            $(cont).find('.imgCropX').val(Math.floor(c.x));
            $(cont).find('.imgCropY').val(Math.floor(c.y));
            $(cont).find('.imgCropWidth').val(Math.floor(c.w));
            $(cont).find('.imgCropHeight').val(Math.floor(c.h));


        }

        $('body').on('change', '.imgFile', changeFiles);



        window.addEventListener('paste', e => {
            let fileInput = document.getElementsByClassName ('fileInputNew')[0];
            fileInput.files = e.clipboardData.files;

          //  $('.imgFile').trigger('change');
            var event = new Event('change', { bubbles: true });
            fileInput.dispatchEvent(event);
        });


    });
</script>