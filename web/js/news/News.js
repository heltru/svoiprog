class News {

    constructor(el) {
        if (el.length) {

            this.el = el;




            $('body').on('click', '.status_prod_descr', function () {
                var id_group = $(this).attr('data-id');//.split('_')[1] ;
                $.ajax({
                    type: "POST",
                    url: "news-descr-change-status",
                    data: {id: id_group, _csrfbe: yii.getCsrfToken()},
                    success: function (data) {
                        $.pjax.reload('#news-descr-grid-ajax');
                    }
                });
            });

            $('body').on('click', '.rem_prod_descr', function () {
                var id = $(this).attr('id');

                $.ajax({
                    type: "POST",
                    url: "news-descr-del",
                    data: {_csrfbe: yii.getCsrfToken(), id: id},
                    success: function (data) {
                        $.pjax.reload('#news-descr-grid-ajax');
                    }
                });
            });


            $('#btnGenerHref').click(function (e) {
                var txt = $('#news-name').val();
                if (txt) {
                    $.ajax({
                        type: "POST",
                        url: "/admin/url/url/make-href",
                        data: {txt: txt, _csrfbe: yii.getCsrfToken()},
                        success: function (data) {
                            if (typeof data == 'object') {
                                if (data.message == 'error') {
                                    $('#hrefInfo').show();
                                    // $('#fieldHref').val('');
                                }
                                if (data.status == 200) {
                                    $('#fieldHref').val(data.data);
                                }


                            }
                        }
                    });
                }
            });
            $('#btnGenerWrHref').click(function (e) {
                var txt = $('#fieldHref').val();

                if (txt) {
                    $.ajax({
                        type: "POST",
                        url: "/admin/url/url/make-href",
                        data: {txt: txt, _csrfbe: yii.getCsrfToken()},
                        success: function (data) {
                            if (typeof data == 'object') {
                                if (data.message == 'error') {
                                    $('#hrefInfo').show();
                                    // $('#fieldHref').val('');
                                }
                                if (data.status == 200) {
                                    $('#fieldHref').val(data.data);
                                }
                            }
                        }
                    });
                }

            });

            $('#btnH1Copy').click(function (e) {
                var txt = $('#news-name').val();
                $('#url-h1').val(txt);
                $('#url-title').val(txt);
                $('#url-description_meta').val(txt);
                $('#url-keywords').val(txt);
            });



        }

        console.log('NEws');
    }


}


let _News;
$(function () {
    _News = new News($('*[data-role="News"]'));
});