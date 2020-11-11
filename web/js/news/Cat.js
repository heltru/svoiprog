class Cat {

    constructor(el) {
        if (el.length) {

            this.el = el;

            this.name = el.find('#newscat-name');


            $('#btnGenerHref').click( (e) => {
                var txt = this.name.val();
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

            $('#btnH1Copy').click( (e)  => {
                var txt = this.name.val();
                $('#url-h1').val(txt);
                $('#url-title').val(txt);
                $('#url-description_meta').val(txt);
                $('#url-keywords').val(txt);
            });



        }

    }


}


let _Cat;
$(function () {
    _Cat = new Cat($('*[data-role="Cat"]'));
});