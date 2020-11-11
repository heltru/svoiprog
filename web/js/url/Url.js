class Url {

    constructor(el) {


        if (el.length) {

            this.el = el;

            $('#clear_domain').click(function (){
                var selectize =  $("#selected_domains")[0].selectize;
                selectize.clear();
            });


            $('#preHrefText').change(function (e) {
                $('#preHref').val(($(this).val()));
            });

            $('#preHrefText').keyup(function (e) {
                $('#preHref').val(($(this).val()));
            });

            $('#url-description_meta').keyup(function (e) {
                $('#countLettersDescr').html($(this).val().length);
            });
            $('#url-description_meta').change(function (e) {
                $('#countLettersDescr').html($(this).val().length);
            });

            $('#url-title').change(function (e) {
                $('#sizeTitleBox').html($(this).val());
            });

            $('#url-title').keyup(function (e) {
                $('#sizeTitleBox').html($(this).val());
                calcWidth();
            });

            $('#url-title').change(function (e) {
                $('#sizeTitleBox').html($(this).val());
                calcWidth();
            });

            function calcWidth() {
                var test = document.getElementById("sizeTitleBox");
                test.style.fontSize = '18px';
                test.style.fontWeight = '400';
                test.style.lineHeight = '22px';
                test.style.fontFamily = 'Arial, Helvetica, sans-serif;';
                var width = (test.clientWidth + 1 ) + "px <= 490";
                $('#msgWidthPx').html(width);

            }


            $('#fieldHref').bind('focusout',function (e) {


                var  url =  $(this).val() ;
                if ($("#preHrefText").val()) url =  $("#preHrefText").val() + "/"+ url;

                if ( $('#url-real_canonical').val()
                    && url != $('#url-real_canonical').val() ){
                    alert('url и rel=canonical не совпадают');
                    return true;
                }

            });

            $('#url-real_canonical').bind('focusout',function (e) {
                var  url =  $(this).val() ;
                if ($("#preHrefText").val()) url =  $("#preHrefText").val() + "/"+ url;


                if ( $('#url-real_canonical').val()
                    && url != $('#url-real_canonical').val() ){
                    alert('url и rel=canonical не совпадают');
                    return true;
                }
            });
        }


        console.log('Url');
    }


}


let _Url;
$(function () {
    _Url = new Url($('*[data-role="Url"]'));
});