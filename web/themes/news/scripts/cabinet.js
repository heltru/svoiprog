$(document).ready(function () {
    // переключение между вкладками
    $('.cabinet #aside ul li').on('click', function() {
        var target_tab;
        $(this).parent().find('li').removeClass('active');
        $(this).addClass('active');
        
        target_tab = $(this).attr('id');
        
        $('.cabinet-tab').removeClass('active-tab');
        
        switch (target_tab) {
            case 'my-info':
                $('#info-tab').addClass('active-tab');
                break;
            case 'my-order':
                $('#order-tab').addClass('active-tab');
                break;
            case 'my-discount':
                $('#discount-tab').addClass('active-tab');
                break;                
        }
    });
    
    // Физическое или юридическое лица
    $('#change-type .button-link').on('click', function() {
        $(this).parent().toggleClass('current-fiz');
        
        if ($(this).parent().hasClass('current-fiz')){
            $('#client-type').text('Физическое лицо');
            $(this).find('span').text('юридическое');
            $('#info-tab form .info-inner').removeClass('active-type');
            $('#info-tab form #fiz').addClass('active-type');
        } else {
            $('#client-type').text('Юридическое лицо');
            $(this).find('span').text('физическое');
            $('#kontakt-lico').val($('#imya-basket').val() + ' ' + $('#familiya-basket').val());
            $('#info-tab form .info-inner').removeClass('active-type');
            $('#info-tab form #yur').addClass('active-type');            
        }
    });
    
    // изменение аватарки
    $(".photo-upload input").change(function() {
        var current_file;
        
        current_file = $(this);
        
        // разрешаем только картинки jpg, png не более 2 мегабайт
        if (!validateFile(current_file)){
            return false;
        } else {
            // клиент меняет фотку. Будет работать через php, поэтому у меня просто подставляет другую картинку
            $(this).parent().parent().find('.img-inner img').attr('src', 'images/clients/avatar/client1.jpg');            
        }
    });
    
    $(".img-inner").on('click', function() {
        $(this).parent().parent().find('.photo-upload input').click();
    });
    
    // изменить пароль
    $('#change-password .button-link').on('click', function() {
        $('#change-password-block').toggleClass('hidden');
        $(this).toggleClass('opened');
    });
    
    // выделение заказов
    $('#order-tab .order-select span').on('click', function() {
        var selected_num = 0;
        if ($(this).closest('.order-item').attr('id') == 'orders-header'){
            $(this).toggleClass('checked');
            
            if ($(this).hasClass('checked')){
                $('#order-tab .order-select span').each(function () {
                    $(this).addClass('checked');
                });
            } else {
                $('#order-tab .order-select span').each(function () {
                    $(this).removeClass('checked');
                });                
            }            
        } else {
            $(this).toggleClass('checked');
            $('#orders-header .order-select span').removeClass('checked');
        }
        
        if ($('#order-tab .order-select span.checked').length > 0){
            selected_num = $('#order-tab .order-select span.checked').length;
            
            if ($('#orders-header .order-select span').hasClass('checked')){
                selected_num--;
            }
            if (selected_num > 0){
                $('#order-tab .remove-info').removeClass('hidden');
                $('#order-num span').text(selected_num);
            }
        } else {
            $('#order-tab .remove-info').addClass('hidden');
        }
    });
    
    // удаление заказов
    $('#remove-orders').on('click', function() {
        $('#order-tab .order-select span.checked').each(function () {
            if ($(this).closest('.order-item').attr('id') != 'orders-header'){
                $(this).closest('.order-item').remove();
            }
        });
        
        $('#order-tab .remove-info').addClass('hidden');
        $('#orders-header .order-select span').removeClass('checked');
        if ($('#order-tab .order-select').length == 1){
            $('#no-orders').removeClass('hidden');
        }
    });
    
});

function validateFile(current_file) {
    var ext;
 
    if (current_file.val() != ''){
        ext = (current_file.val().split('.').pop()).toLowerCase();

        if ((ext != 'png') && (ext != 'jpg') && (ext != 'jpeg')){
            $('#message .modal-title').text('Ошибка');
            $('#message .message-text').text('Допускаются файлы в формате PNG, JPG, JPEG');
            $('#message .message-text').removeClass('hidden');        
            $('#message .button-link').addClass('hidden');
            $('#message .button').text('OK');
            $.fancybox.open({src: '#message'});            
            return false;
        }
    }
    
    if((current_file).prop('files')[0].size > 2097152){
        $('#message .modal-title').text('Ошибка');
        $('#message .message-text').text('Файл должен быть не более 2 мегабайт');
        $('#message .message-text').removeClass('hidden');        
        $('#message .button-link').addClass('hidden');
        $('#message .button').text('OK');
        $.fancybox.open({src: '#message'}); 
        return false;
    }
    
    return true;    
}