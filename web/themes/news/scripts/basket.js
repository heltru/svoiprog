$(document).ready(function () {

    var stage = 'one';
    
    // если нет выбранных товаров, то показываем сообщение об этом и скрываем вкладки
    if ($('.product-basket').length > 1) {
        $('#basket-tabs').removeClass('hidden');
        $('#basket-header').removeClass('hidden');
        $('.basket .info-block').removeClass('hidden');
        $('#basket-placeholder').addClass('hidden');
    } else {
        $('#basket-tabs').addClass('hidden');
        $('#basket-header').addClass('hidden');
        $('.basket .info-block').addClass('hidden');
        $('#basket-placeholder').removeClass('hidden');
    }
    
  //  count_sum();
    
    // фильтр цены: слайдер
    $(function() {

        $("#slider-points").slider({
            range: 'min',
            min: 0,
            max: 2100,
            step: 100,
            value: 2100,           
            slide: function(event, ui) {
                $('#use-points .val').text(ui.value.toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, "$1 "));
            },
            change: function(event, ui) {
                $('#use-points .val').text(ui.value.toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, "$1 "));
            }
        });

        //$('#use-points .val').text(ui.value.toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, "$1 "));

    });    
    
    // Удалить конкретный товар
    $('.basket').on('click', '.product-basket-remove p', function(e) {
        $.product = $(this).closest('.product-basket').data('id');
        
        $('#dialog .modal-title').text('Удалить этот товар?');  

        $.fancybox.open({src: '#dialog'});        
    });
    
    // Кнопка "Отмена" в диалоге на удаление
    $('#dialog .button').on('click', function() {
        $.fancybox.close();        
    });
    
    // Кнопка "Удалить" в диалоге на удаление
    $('#dialog .button-link').on('click', function() {
        $('.product-basket').each(function () {
            var current_product;
            current_product = $(this).data('id');
            if (current_product == $.product) {
                $(this).remove();
                removeItem( $(this).data('id') , 'mod' );
                return false;
            }
        });
        if ($('.product-basket').length == 1) {
            $('#basket-tabs').addClass('hidden');
            $('#basket-header').addClass('hidden');
            $('.basket .info-block').addClass('hidden');
            $('#basket-placeholder').removeClass('hidden');                    
        }        
        
        count_sum();
        
        $.fancybox.close();        
    });

    function removeItem(id,type,setid,doptype) {
        var sendData = {
            id:id,
            type:type,
            _csrffe:$('meta[name=csrf-token]').attr('content')
        };
        if ( setid ){
            sendData.setid = setid;
        }
        if ( doptype ){
            sendData.doptype = doptype;
        }
        $.ajax({
            type:"POST",
            url:"/basket/remove-item",
            data:sendData,
            success:function (obj) {
                if (typeof obj == 'object'){
                    if ( 'status' in  obj && 'totalCount' in  obj && 'totalSumm' in  obj
                        && 'totalDiscount'){
                        if (obj.status == 'ok'){
                         //   update_fields_summary(obj);
                            count_sum();

                            $('#your-basket-tab').html($(obj.html).filter('#your-basket-tab' ).html());

                        }
                    }
                }
            }
        });
    }
    
    // прибавление/убавление количества товара
    $('.basket').on('click', '.count-control .remove, .count-control .add', function(e) {
        var items = 1,
            input,
            price_one = 0,
            sum = 0;
        input = $(this).siblings('.count');
        if (input.lengt == 0) {
            return;
        }
        
        items = parseInt(input[0].value,10) + (e.target.className === 'remove'?-1:1);
        if(items < 1 || isNaN(items)){
            items = 1;
        }
        input[0].value = items;
        
        /*price_one = parseInt($(this).closest('.product-basket').find('.price-val').text().replace(/\s+/g,''));
        sum = price_one * items;
        $(this).closest('.product-basket').find('.sum-val').text(sum.toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, "$1 "));
        */

        countItem($(this).closest('.product-basket').data('id'),'mod',items);

      //  count_sum();

    });

    $('#your-basket-tab').on('paste', 'input.count',  function (e) {
        e.preventDefault();
    });

    $('#your-basket-tab').on('change', 'input.count',  function (e) {

        // valid count
        var count = Number($(this).val());
        if ( ! count ) {
            count = 1;
            $(this).val(count);
        }
        countItem($(this).closest('.product-basket').data('id'),'mod',count);
    });

    $('#your-basket-tab').on('keydown', 'input.count',  function (e) {

        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
            // Allow: Ctrl+A, Command+A
            (e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) ||
            // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
            // let it happen, don't do anything

            return ;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    $('#your-basket-tab').on('keyup', 'input.count',  function (e) {
        if ( Number($(this).val() ) > 300  ) $(this).val(300);
    });

    function countItem(id,type,count) {

        $.ajax({
            type:"POST",
            url:"/basket/count-item",
            data:{
                id:id,
                type:type,
                count:count,
                _csrffe:$('meta[name=csrf-token]').attr('content')
            },
            success:function (obj) {

                if (typeof obj == 'object'){
                    if ( 'status' in  obj && 'totalCount' in  obj && 'totalSumm' in  obj
                        && 'totalDiscount' ){
                        if (obj.status == 'ok'){
                           // update_fields_summary(obj);  count_sum(obj);
                            $('#your-basket-tab').html($(obj.html).filter('#your-basket-tab' ).html());

                            $("#your-basket-tab").find("[data-type='" + type + "']").each(function (i,v) {
                                if ( $(v).data('id') == id ){
                                    $(v).find('input.count').focus();
                                }
                            });
                        }

                    }
                }
            }
        });
    }
    
    // Окно с накопительными баллами
    $('#discount-points .button').on('click', function() {
        $.fancybox.open({src: '#points'});        
    });
    
    // Применение накопительных баллов
    $('#points .button').on('click', function() {
        var available_points = 2100,
            used_points = 0;
        
        used_points = $('#use-points .val').text();
        
        $('.order-total .order-total-points .val span').text(used_points);
        $('#discount-points-reserv .val').text(used_points);
        
        count_total();
        
        $.fancybox.close();        
    });
    

    // добавление товаров "Вам может пригодиться" со страницы
    $('.basket').on('click', '.product .button-link', function(e) {
        var dop_id;       

        dop_id = $(this).data('id');
        
        add_product(dop_id);

    }); 
    
    // быстрый просмотр товара из "Вам может пригодиться"
    $('.basket .product .img-container').on('click', function() {
        var dop_id, current_dop;
        
        dop_id = $(this).closest('.product').find('.button-link').data('id');
        current_dop = $(this).closest('.product');
        
        $('#quick-view').find('.button').data('id', dop_id);
        
        info_product(current_dop);
        
        dop_img = info_product(current_dop)[0];
        dop_name = info_product(current_dop)[1];
        dop_descr= info_product(current_dop)[2];
        dop_price = info_product(current_dop)[3];
        
        $('#quick-view .image-container img').attr('src', dop_img);
        $('#quick-view .product-name').text(dop_name);
        $('#quick-view .new-price .sum').text(dop_price);
        $('#quick-view .descr').text(dop_descr);
        
        $.fancybox.open({src: '#quick-view'});        
    });
    
    // добавление товара из быстрого просмотра
    
    $('#quick-view .button').on('click', function() {
        var dop_id;       

        dop_id = $(this).data('id');
        
        add_product(dop_id);
        
        $.fancybox.close();
    });
    
    // кнопка "Оформить заказ"
    $('#make-order').on('click', function() {
        $('#your-basket').removeClass('active-step');
        $('#your-basket').addClass('filled available');
        $('#your-basket-tab').removeClass('active-tab');
        $('#basket-detailes').addClass('active-step');
        $('#basket-detailes-tab').addClass('active-tab');
        $('#recommended-basket').addClass('hidden');
        
        // доставка транспортной компанией по умолчанию
        $('#transport').prop('checked', true);
        
        $('html, body').animate({ scrollTop: $('#basket-steps').offset().top }, 500);
    });
    
    // изменить заказ
    $('#your-basket').on('click', function() {        
        // переход к этой вкладке возможен только с вкладки "Детали получения"
        if ($(this).hasClass('available')) {
            // переключение вкладок
            $('#your-basket').removeClass('available');
            $('#your-basket').addClass('active-step');
            $('#your-basket-tab').addClass('active-tab');
            $('#basket-detailes').removeClass('active-step');        
            $('#basket-detailes-tab').removeClass('active-tab');
            $('#recommended-basket').removeClass('hidden');
        }

    });    
    
    // физическое или юридическое лицо
    $('#type-controller p').on('click', function() {
        var target_tab;
        $('#type-controller p').removeClass('active-type');
        $(this).addClass('active-type');
        
        $('.client-type').removeClass('active-tab');
        target_tab = $(this).attr('id');
        
        if (target_tab == 'fiz-btn') {
            $('#fiz').addClass('active-tab');
        } else {
            $('#yur').addClass('active-tab');
        }
    });
    
    // заполение личных данных
    $('#personal-info .button').on('click', function() {
        var name, phone, email;
        name = $('#imya-basket').val() + ' ' + $('#familiya-basket').val();
        phone = $('#phone-basket').val();
        email = $('#email-basket').val();
        $('.client-name').text(name);
        $('.client-phone').text(phone);
        
        if ((name != ' ')&&(phone != '')){
            $('#personal-info').addClass('small filled');
            $('#delivery-info').removeClass('small');
            $('#pay-info').addClass('small');
        }
    });
    
    // изменение личных данных
    $('#personal-info .button-link').on('click', function() {
        $('#personal-info').removeClass('small filled');
        $('#delivery-info').removeClass('filled');
        $('#delivery-info, #pay-info').addClass('small');
    });    
    
    // выбор способа доставки
    $('#delivery-info .radio-label').on('click', function() {
        var del;
        $('#delivery-info .radio-label').removeClass('active');
        $(this).addClass('active');
        del = $('input[name=delivery]:checked').val();
        if (del == 'Самовывоз'){
            $('.delivery-additional').addClass('hidden');
            $('#client-address').val(''); 
        } else {
            $('.delivery-additional').removeClass('hidden');
        }
    });
    
    // cпособ доставки
    $('#delivery-info .button').on('click', function() {        
        var del, addr;
        del = $('input[name=delivery]:checked').val();
        addr = $('#client-address').val();        
        
        if ($('#delivery-info .radio-label.active').length != 0){
            $('.selected-delivery').text(del);
            if (addr != ''){
                $('.delivery-address').text(addr);
            } else {
                $('.delivery-address').text('');
            }
            $('#delivery-info').addClass('small filled');
            $('#pay-info').removeClass('small');      

            // слайдер способов оплаты
            $('#payment-slider').slick({
                infinite: true,
                slidesToShow: 5,
                slidesToScroll: 1,
                arrows: true,
                dots: false,
                focusOnSelect: true,
                responsive: [
                    {
                        breakpoint: 979,
                        settings: {
                            slidesToShow: 3
                        }
                    }, {
                        breakpoint: 479,
                        settings: {
                            slidesToShow: 2
                        }
                    }                    
                ]
            });             
        }
    });
    
    // изменение способа доставки
    $('#delivery-info .button-link').on('click', function() {
        $('#personal-info .button').click();
        if (!$('#personal-info').hasClass('small')){
            $('#pay-info').addClass('small');
        }
    });
    
    // завершение ввода данных
    $('#pay-info #final-button').on('click', function() {
        $('#pay-info').addClass('small filled');
        $('#basket-steps').addClass('hidden');
        $('h1').text('Спасибо за покупку');
        // если заполнен адрес, показываем его
        if ($('#client-address').val() != ''){
            $('#delivery-addr').removeClass('hidden');
            $('#delivery-addr .delivery-address').text($('#client-address').val());
        } else {
            $('#delivery-addr').addClass('hidden');
        }
        $('#order-pay').text($('#pay-info').find('.slick-current span').text());
        $('#order-img').attr('src', $('.slick-current img').attr('src'));
        $('#basket-thanks').addClass('active-step');
        $('#basket-detailes-tab').removeClass('active-tab');
        $('#basket-thanks-tab').addClass('active-tab');
        
        // обнуляем количество товаров в корзине
        $('#basket-inner span').text(0);
        
        $('html, body').animate({ scrollTop: $('h1').offset().top }, 500);
    });

});

// добавление товаров "Вам может пригодиться"
function add_product(dop_id) {
    var current_dop, dop_img, dop_name, dop_descr, dop_price, in_list = false;    
    
    // проверям есть ли в корзине товар с таким ИД, если есть - увеличиваем количество        
    $('.product-basket').each(function () {
        var id;
        id = $(this).data('id');
        if (id == dop_id){
            $(this).find('.add').click();
            in_list = true;
            return false;
        }
    });        

    // если не нашли такой ИД в корзине, добавляем новый блок
    if (in_list == false){
        
        $('.basket .product-columns .button-link').each(function () {
            var id;
            id = $(this).data('id');
            if (id == dop_id){
                current_dop = $(this).closest('.product');
                return false;
            }
        });
        
        info_product(current_dop);
        
        dop_img = info_product(current_dop)[0];
        dop_name = info_product(current_dop)[1];
        dop_descr= info_product(current_dop)[2];
        dop_price = info_product(current_dop)[3];

        $('<div class="product-basket" data-id="' + dop_id + '"><div class="product-basket-info"><div class="img-container"><img src="' + dop_img + '" /></div><div class="text"><p class="product-name">' + dop_name +'</p><p>' + dop_descr + '</p></div></div><div class="product-basket-price"><p><span class="price-val">' + dop_price + '</span> руб.</p></div><div class="product-basket-num"><div class="count-control"><span class="remove">–</span><input class="count" autocomplete="off" step="1" min="1" value="1" type="number"><span class="add">+</span></div></div><div class="product-basket-sum"><p><span class="sum-val">' + dop_price + '</span> руб.</p></div><div class="product-basket-remove"><p>&nbsp;</p></div></div>').insertAfter($('.product-basket').last());            
    }            

    count_sum();
    
    $('#basket-tabs').removeClass('hidden');
    $('#basket-header').removeClass('hidden');
    $('.basket .info-block').removeClass('hidden');
    $('#basket-placeholder').addClass('hidden');    
}

// получение информации о товаре
function info_product(current_dop) {
    var dop_img, dop_name, dop_descr, dop_price;
    
    dop_img = current_dop.find('.img-container img').attr('src');
    dop_name = current_dop.find('.product-name').text();

    dop_descr= current_dop.find('.product-name').data('descr');

    dop_price = current_dop.find('.new-price .sum').text();
    
    return [dop_img, dop_name, dop_descr, dop_price];
}

// подсчет стоимости выбранных товаров
function count_sum() {
    var sum = 0,
        discount = 0,
        points = 0,
        total = 0,
        items = 0;
    
    // товаров на сумму
    $('.product-basket .sum-val').each(function () {
        sum += parseInt($(this).text().replace(/\s+/g,''));
    });
        
    // количество товаров
    $('.count').each(function () {
        items += parseInt($(this).val());
     });
    //$('#basket-inner span').text(items);
    
    $('.order-total .order-total-itogo .val span').text(sum.toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, "$1 "));
    
    // бонусы оптовикам
    if (sum < 50000){
        $('#discount-percent span').text(0); //действующая скидка
        $('#discount-more').text((50000-sum).toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, "$1 ")); // сумма, необходимая для получения большей скидки
        $('#discount-more-val').text('5%'); // бОльшая скидка
        $('.delivery-sum').text((100000-sum).toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, "$1 ")); // сумма, необходимая для получения бесплатной доставки
        $('.order-total .order-total-discount .val span').text(0); // сумма скидки
        $('#discount-info .buy-more').removeClass('hidden'); // показываем, какой суммы не хватает до следующей скидки и бесплатной доставки
        $('#discount-info .final').addClass('hidden'); // скрываем сообщение, что максимальная выгода получена
        $('#delivery-pay').removeClass('hidden'); // сообщение на форме о стоимости доставки
        $('#delivery-free').addClass('hidden'); // сообщение на форме о бесплатной доставке
    } else if ((sum >= 50000)&&(sum < 100000)){
        $('#discount-percent span').text(5);
        $('#discount-more').text((100000-sum).toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, "$1 "));
        $('#discount-more-val').text('10%');
        $('.delivery-sum').text((100000-sum).toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, "$1 "));
        $('.order-total .order-total-discount .val span').text((parseInt(sum*0.05)).toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, "$1 "));
        $('#discount-info .buy-more').removeClass('hidden');
        $('#discount-info .final').addClass('hidden');
        $('#delivery-pay').removeClass('hidden');
        $('#delivery-free').addClass('hidden');      
    } else if ((sum >= 100000)&&(sum < 200000)){
        $('#discount-percent span').text(10);
        $('#discount-more').text((200000-sum).toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, "$1 "));
        $('#discount-more-val').text('15%');        
        $('.order-total .order-total-discount .val span').text((parseInt(sum*0.1)).toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, "$1 "));
        $('#discount-info #discount-column .buy-more').removeClass('hidden');
        $('#discount-info #discount-column .final').addClass('hidden');        
        $('#discount-info #delivery-column .buy-more').addClass('hidden');
        $('#discount-info #delivery-column .final').removeClass('hidden');
        $('#delivery-pay').addClass('hidden');
        $('#delivery-free').removeClass('hidden');        
    } else if (sum >= 200000){
        $('#discount-percent span').text(15);
        $('.order-total .order-total-discount .val span').text((parseInt(sum*0.15)).toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, "$1 "));
        $('#discount-info .buy-more').addClass('hidden');
        $('#discount-info .final').removeClass('hidden');
        $('#delivery-pay').addClass('hidden');
        $('#delivery-free').removeClass('hidden');
    }
    
    count_total();
}

// подсчет общей суммы заказа с учетом скидок и накопительных баллов
function count_total() {
    var total_sum = 0;
    
    total_sum = parseInt($('#your-basket-tab .order-total .order-total-itogo .val span').text().replace(/\s+/g,'')) - parseInt($('#your-basket-tab .order-total .order-total-discount .val span').text().replace(/\s+/g,'')) - parseInt($('#your-basket-tab .order-total .order-total-points .val span').text().replace(/\s+/g,''));
    
    $('.order-total .order-total-sum .val span, #order-sum').text(total_sum.toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, "$1 "));
}