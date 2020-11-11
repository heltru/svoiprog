$(function () {

    $.fn.select2.defaults.set('language', 'ru');


    $('*[data-role="select2"]').select2({
        ajax: {
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params['term'],
                    page: params['page']
                };
            },
            processResults: function (data, params) {
                params['page'] = params['page'] || 1;

                if (data['success']) {
                    // to do nothing..
                    // console.log('ok');
                } else {
                    // console.log('error');

                    let $modalError;
                    if (data['error_code']) {
                        $modalError = $('#modalError-' + data['error_code']);
                    }

                    if ($modalError && $modalError.length) {
                        $modalError.modal('show');
                        $('*[data-role="select2"]').select2('close');
                    } else if (data['message']) {
                        notify(data['message'], 'danger');
                    }
                }

                return {
                    results: data['items'],
                    pagination: {
                        more: (!data['end'])
                    }
                };
            },
            cache: true
        },
        templateResult: function (data) {
            if (data['loading']) return data['text'];
            return data['text'];
        },
        templateSelection: function (data) {
            return data['text'];
        }
    });



    $('*[data-role="msbr_new-object"]').click(function (e){
        $('#modalSbrObject').modal('show');
    });
    //add Object
    $('*[data-role="btn_add-object"]').click(function (e) {

        let form = $('#modalSbrObject').find('form');

        $.ajax({
            url: form.attr('action'),
            type: "POST",
            data: form.serialize(),
            beforeSend: (form) => {
            },
            success: (data) => {
                if (data.success) {
                    window.location.reload();
                }
            },
            complete: (form) => {
            }
        });
    });


    $('*[data-role="msbr_smeta-add"]').click(function (e){
        $('#modalSbrSmeta').modal('show');
        $('#modalSbrSmeta').find('input[name ="object_id"]').val($(this).attr('data-object_id'));

    });
    //add Object
    $('*[data-role="btn_add-smeta"]').click(function (e) {

        let form = $('#modalSbrSmeta').find('form');

        $.ajax({
            url: form.attr('action'),
            type: "POST",
            data: form.serialize(),
            beforeSend: (form) => {
            },
            success: (data) => {
                if (data.success) {
                    window.location.href = data.location;
                }
            },
            complete: (form) => {
            }
        });
    });







});
function  clearForm($form){

    $form.find('input').not(':hidden').each(function(){
        $(this).val('');
    });
    $form.find('select').each(function(){
        $(this).val('');
    });
}





