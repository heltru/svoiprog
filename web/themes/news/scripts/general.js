$(document).ready(function () {

    var state  = 'null';

    $('#phone-order, #phone-reg, #phone-call').mask("9(999)999-99-99");


    $('#footer-form input[name*="phone"]').mask("9(999)999-99-99");

    $('#help-block input[name*="phone"]').mask("9(999)999-99-99");


    //отправить сообщение  /help
    $('#help-block .button').click(function (e) {

            console.log('click');
            e.preventDefault();
            var url = $('#help-block form').attr('action'); //'https://apispn.ru/action/call.php';

            var name = $('#help-block form').find('#name-help').val();
            var email = $('#help-block form').find('#email-help').val();
            var phone = $('#help-block form input[name*="phone"]').val();
            var message = $('#comment-help').val();

            var type = 'helpForm';
            console.log(name,phone,url);
            if (  phone || email  ) {

                $.ajax({
                    url: url,
                    type: 'post',
                    // dataType: 'json',
                    data: {name: name, phone: phone,email:email,type:type,message:message}/*,
                     /*success: function(data) {
                     console.log(data);
                     },*/
                    /*complete:function (data) {
                     if (data.readyState == 4){
                     console.log('callback');
                     window.dataLayer.push({'event': 'callback_submit'});

                     }

                     }/**/
                }).done(function (data) {
                 //   if (data.response.status == 'success') {
                        $.fancybox.close();
                        $('#message .modal-title').text('Письмо отправлено!');
                        $('#message .message-text').html('Скоро с Вами свяжутся наши менеджеры');
                        $('#message .message-text').removeClass('hidden');
                        $('#message .button-link').addClass('hidden');
                        $('#message .button').text('Ок');
                        $.fancybox.open({src: '#message'});
                    //    window.dataLayer.push({'event': 'callback_submit'});
                 //   }

                });


                $('#name-help').val('');
                $('#email-help').val('');
                $('#help-block form input[name*="phone"]').val('');
                $('#comment-help').val('');
            }
        }
    );

    
    // при загрузке страницы скрывать подменю на мобильных версиях
    if (document.documentElement.clientWidth > 767){
        $('#header').removeClass('mobile');
        $('#footer-menu').removeClass('visible');
    } else {
        $('#header').addClass('mobile');                                
    }    
    
    // переход по ссылке
    $('.link').bind("mousedown", function(event) {
        if(event.which == 1) {
            if ($(this).data('link')  !=  undefined)
                window.location.href = $(this).data('link');
        } else {
            window.open($(this).data('link'), '_blank');
        }
    });    
    
    // поиск города
    $('#selected-city').on('click', function() {
        $('#city-block').addClass('visible');
        $('<a id="overlay" style="position: absolute; left: 0; top: 0; width:100%; height: 100%; z-index: 15; "></a>').appendTo('body');
    });
    
    // выбор города из списка
    $('#city-block li').on('click', function() {
        var city = $(this).text();
        console.log(city);
        var url = $("#user_city").attr('action');
        if (city && url){

            $.ajax({
                url:url,
                data:{city_id:city}
            });
        }
        $('#selected-city').text($(this).text());

        $('#city-block').removeClass('visible');
        $('#overlay').remove();
    });

    $("#user_city").submit(function (e){

       // e.preventDefault();
    });
    
    // показываем результаты поиска
    $('#search').bind("change keyup input click", function(event) {
   /*     $('#search-results').addClass('visible');
        $('<a id="overlay" style="position: absolute; left: 0; top: 0; width:100%; height: 100%; z-index: 15; "></a>').
       appendTo('body');*/
    });

    $('body').on('keyup', '#search', function(e) {
        e.preventDefault();
        var q = String( $(this).val() );
        q =  $.trim(q).replace(/[^a-zA-Zа-яА-Я0-9]/g,'');
        if ( q.length < 3) {
            $('#search-results-inner').html('');
            $('#search-results').removeClass('visible');
            return;
        }


        var url = String($('#search-main-form').data('ajax'));

        if (q && url){
            $.ajax({
                type:"GET",
                url:url,
                data:{q:q,_csrffe:$('meta[name=csrf-token]').attr('content')},
                success:function (data) {
                    if ( typeof data == 'object' && 'status' in data && 'data' in data){
                        if (data.status == 200){
                            $('#search-results-inner').html('');
                            $('#search-results-inner').html($(data.data).find('#search-results-inner').html());
                            $('#search-results').addClass('visible');

                            $('.link').bind("mousedown", function(event) {
                                if(event.which == 1) {
                                    window.location.href = $(this).data('link');
                                } else {
                                    window.open($(this).data('link'), '_blank');
                                }
                            });
                        }  else {
                            $('#search-results').removeClass('visible');
                        }
                    }
                }
            });
        }
    });

    // галерея
    $('.fancy-gallery').fancybox({
        loop: true,
        animationEffect: 'zoom',
        transitionEffect: 'slide',
        transitionDuration: 500
    });

    //обработка форм
    // заявка
    $('.fancybox').on('click', function() {
        $('#quick-order form').attr('data-owner','slider');
        $.fancybox.open({src: '#quick-order'});
        window.dataLayer.push({'event': 'akciya_click'});
    });
    
    // заказ звонка
    $('.fancybox-call').on('click', function() {
        $('#quick-order form').attr('data-owner','call');
        $.fancybox.open({src: '#call-order'});
    });

    //регистрация оптовика
    $('#register-modal .button').on('click', function(e){
        $('#quick-order form').attr('data-owner','order');
        $.fancybox.open({src: '#quick-order'});
    });

    //я опотовик
    $('#product-features .button, #product-info .button').on('click', function(e){

        $('#quick-order form').attr('data-owner','order');
        $.fancybox.open({src: '#quick-order'});



    });



    // callbackForm event click
    // оставить заявку
    $('#quick-order .button, #call-order .button, #footer-form form .button, #subscriptionMainForm .button').on('click', function(event) {
        event.preventDefault();
        state = '';
        var defTitle = 'Спасибо за заявку';

        var owner = $(event.currentTarget).parent().attr('data-owner');

        console.log(owner);

        if ( String(owner).length ){
            var $form  =  $(event.currentTarget).parent();

            var url = $form.attr('action');
            var valid = false;
            if (String(url).length){
                var data = {};
                switch (owner){
                    case 'call':
                        data.name = $form.find('#name-call').val();
                        data.phone = $form.find('#phone-call').val();
                        data.type = 'call';
                        valid = data.name && data.phone;
                        state = 'call';
                        window.dataLayer.push({'event': 'zakaz_click'});
                        break;
                    case 'order':
                        data.name = $form.find('#name-order').val();
                        data.phone = $form.find('#phone-order').val();
                        data.email = $form.find('#email-order').val();
                        data.type = 'bid';
                        valid = data.name && data.phone;
                        state = 'bid';
                        window.dataLayer.push({'event': 'zakaz_click'});

                        var _tmr = window._tmr || (window._tmr = []);
                        _tmr.push({'type':'itemView','pagetype':'purchase','productid':$("#curr_prod_mod").val(),
                            'totalvalue':$("#curr_prod_mod_price").val(),'list':1});

                        console.log({'type':'itemView','pagetype':'purchase','productid':$("#curr_prod_mod").val(),
                            'totalvalue':$("#curr_prod_mod_price").val(),'list':1});

                        var _tmr = window._tmr || (window._tmr = []);
                        _tmr.push({'type':'itemView','pagetype':'cat','productid':$("#curr_prod_mod").val(),
                            'totalvalue':$("#curr_prod_mod_price").val(),'list':1});

                        console.log({'type':'itemView','pagetype':'cat','productid':$("#curr_prod_mod").val(),
                            'totalvalue':$("#curr_prod_mod_price").val(),'list':1});

                        break;
                    case 'slider':
                        data.name = $form.find('#name-order').val();
                        data.phone = $form.find('#phone-order').val();
                        data.email = $form.find('#email-order').val();
                        data.type = 'action';
                        valid = data.name && data.phone;
                        state = 'slider';
                        window.dataLayer.push({'event': 'akciya_click'});
                        break;
                    case 'footer':
                        data.name = $form.find('input[name*="name"]').val();
                        data.phone = $form.find('input[name*="phone"]').val();
                        data.type = 'bid';
                        valid = data.name && data.phone;
                        window.dataLayer.push({'event': 'footer_click'});
                        break;
                    case 'subscription':
                        defTitle = 'Спасибо за подписку!';
                        data.email =  $form.find('input[name*="subscribe"]').val();
                        data.type = 'subscription';
                        var pattern = /^([a-z0-9_\.-])+@[a-zа-я0-9-]+\.([a-zа-я]{2,4}\.)?[a-zа-я]{2,4}$/i;
                        valid = pattern.test( data.email );
                        break;
                }

                var txtMsg = 'Наш менеджер позвонит Вам в течение 5 минут';
                if ( owner == 'subscriptionForm'){
                    $('#message .message-text').addClass('hidden');
                }
                $('#message .modal-title').text(defTitle);

                $('#message .button-link').addClass('hidden');
                $('#message .button').text('ОК');
                $('#message .message-text').text(txtMsg);

                $('#message .message-text').removeClass('hidden');

                if (valid){
                    switch (owner){
                        case 'call':
                            window.dataLayer.push({'event': 'zakaz_submit'});
                            break;
                        case 'order':
                            window.dataLayer.push({'event': 'zakaz_submit'});
                            break;
                        case 'slider':
                            window.dataLayer.push({'event': 'akciya_submit'});
                            break;
                        case 'footer':
                            window.dataLayer.push({'event': 'footer_submit'});
                            break;
                        case 'subscription':
                            $('#message .message-text').addClass('hidden');
                            break;
                    }


                    $.ajax({
                        url:url,
                        type:"POST",
                        data:data,
                        success:function (data){
                            console.log(data);


                            $.fancybox.open({src: '#message'});

                            $('#subscriptionMainForm input[name*="subscribe"]').val('');
                            $form.find('input[name*="phone"]').val('');
                            $form.find('input[name*="name"]').val('');
                            $form.find('input[name*="email"]').val('');
                            state = '';
                        }
                    });
                } else {
                    $.fancybox.close();
                    $('#message .modal-title').text('Заполните все данные');
                    $('#message .message-text').addClass('hidden');
                    $('#message .button-link').addClass('hidden');
                    $('#message .button').text('Ок');
                    $.fancybox.open({src: '#message'});

                }

            }
        }
    });

    // вход в личный кабинет
    $('#log-in').on('click', function() {
        state = 'login';
        $('#to-cabinet .cab-tab').removeClass('active');
        $('#to-cabinet #cabinet-log-in').addClass('active');

        var type = $('#register').data('type');

        if (type == 'client'){
            window.location = "/cabinet/view";
        } else {
            $.fancybox.open({src: '#to-cabinet'});
        }

    });

    $('#cabinet-log-in .button').on('click',function (e){

        e.preventDefault();
        var email = $("#email-log").val();
        var pass = $("#pass-log").val();

        if (email && pass){
            $.ajax({
                type:"POST",
                url:"/site/login-client",
                data:{email:email,pass:pass,_csrffe:$('meta[name=csrf-token]').attr('content')},
                success:function (data) {
                    if (typeof data == 'object'){
                        if (data.status == 400){

                            var errText = '';

                            if ( 'data' in data && data.data instanceof Object ){
                                $.each(data.data , function (i,v) {
                                    if (v instanceof  Array && v.length > 0){
                                        errText +=  v[0];
                                    }
                                });
                            }
                            $.fancybox.close();
                            $('#message .message-text').text(errText);
                            $('#message .modal-title').text('Неверныйе данные');
                            $('#message .message-text').removeClass('hidden');
                            $('#message .button-link').addClass('hidden');
                            $('#message .button').text('Ок');
                            $.fancybox.open({src: '#message'});
                        }
                        if (data.status == 200){
                            window.location = "/cabinet/view";
//                            location.reload();
                        }
                    } else {
                        $.fancybox.close();
                        $('#message .modal-title').text('Ошибка сервера или сети');
                        $('#message .message-text').addClass('hidden');
                        $('#message .button-link').addClass('hidden');
                        $('#message .button').text('Ок');
                        $.fancybox.open({src: '#message'});
                        state = '';
                    }

                }
            });
        }
    });

    $('#cabinet-reg .button').on('click',function (e){
        state = 'register';
        e.preventDefault();
        var name = $("#name-reg").val();
        var phone = $("#phone-reg").val();
        var email = $("#email-reg").val();
        var pass = $("#pass-reg").val();
        var pass_repeat = $("#pass-repeat").val();

        if ( name &&  phone && email /*&& pass && pass_repeat*/ ){
            var data = {
                name:name,
                phone:phone,
                email:email,
            /*    pass:pass,
                pass_repeat:pass_repeat,*/
                _csrffe:$('meta[name=csrf-token]').attr('content')
            };

            $.ajax({
                type:"POST",
                url:"/site/register-client",
                data:data,
                success:function (data) {

                    if (typeof data == 'object'){
                        if (data.status == 400){

                            var errText = '';

                            if ( 'data' in data && data.data instanceof Object ){
                                $.each(data.data , function (i,v) {
                                    if (v instanceof  Array && v.length > 0){
                                        errText +=  v[0] + '<br>';
                                    }
                                });
                            }
                            $('#message .message-text').html(errText);

                          //  $.fancybox.close();
                            $('#message .modal-title').text('Неверные данные');
                            $('#message .message-text').removeClass('hidden');
                            $('#message .button-link').addClass('hidden');
                            $('#message .button').text('Ок');
                            $.fancybox.open({src: '#message'});
                        }
                        if (data.status == 200){
                            //window.location = "/cabinet/view";
                            $.fancybox.close();
                            $('#message .modal-title').text('Спасибо за заявку!');
                            $('#message .message-text').html('Скоро с Вами свяжутся наши менеджеры');
                            $('#message .message-text').removeClass('hidden');
                            $('#message .button-link').addClass('hidden');
                            $('#message .button').text('Ок');
                            $.fancybox.open({src: '#message'});
                            state = '';

                        }
                    } else {
                        $.fancybox.close();
                        $('#message .modal-title').text('Ошибка сервера или сети');
                        $('#message .message-text').addClass('hidden');
                        $('#message .button-link').addClass('hidden');
                        $('#message .button').text('Ок');
                        $.fancybox.open({src: '#message'});

                    }
                }
            });
        } else {
            $.fancybox.close();
            $('#message .modal-title').text('Заполните все данные');
            $('#message .message-text').addClass('hidden');
            $('#message .button-link').addClass('hidden');
            $('#message .button').text('Ок');
            $.fancybox.open({src: '#message'});
        }
    });


    $("#register").on('click', function() {
        var type = $(this).data('type');
        if (type == 'client'){
            $('#logout_form').submit();
        }
    });

    // регистрация
    $('#register').on('click', function() {

        var type = $(this).data();
        $('#to-cabinet .cab-tab').removeClass('active');
        $('#to-cabinet #cabinet-reg').addClass('active');
        var type = $(this).data('type');
        if (type != 'client'){
            $.fancybox.open({src: '#to-cabinet'});
        }

    });


    // переключение между входом и регистрацией
    $('.cab-tab .button-link').on('click', function() {
        var active_tab;
        active_tab = $(this).closest('.cab-tab').attr('id');

        if(active_tab == 'cabinet-log-in'){
            $('#to-cabinet .cab-tab').removeClass('active');
            $('#to-cabinet #cabinet-reg').addClass('active');
        } else {
            $('#to-cabinet .cab-tab').removeClass('active');
            $('#to-cabinet #cabinet-log-in').addClass('active');
        }
    });


    $(document).on('click', '.product-container .button, #product-info .inbasket', function(event){
    // добавление товара в корзину

        event.preventDefault();

        $.fancybox.close();
        $('#message .modal-title').text('Товар добавлен в корзину');
        $('#message .message-text').addClass('hidden');
        $('#message .button-link').removeClass('hidden');
        $('#message .button').text('Продолжить покупки');
        $.fancybox.open({src: '#message'});



        addBasketItem(
            $(this).data('id'),$(this).data('type'),
            $(this).parent().parent().find('.count-control input').val()
        );



        console.log('ggg');

    });

    function addBasketItem(id,type,count) {
        $.ajax({
            type:"POST",
            url:"/basket/add-item",
            data:{
                id:id,
                type:type,
                count:count,
                _csrffe:$('meta[name=csrf-token]').attr('content')
            },
            success:function (obj) {

                if (typeof obj == 'object'){
                    if ( 'status' in  obj && 'totalCount' in  obj && 'totalSumm' in  obj  ){
                        if (obj.status == 'ok'){

                            $('#basket-inner span').text(obj.totalCount.toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, "$1 "));
                          //  $('#basket-info-sum span').text(obj.totalSumm.toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, "$1 "));

                        }
                    }
                }
            }
        });
    }

    // закрыть сообщение
    $('#message .button').on('click', function() {
        $.fancybox.close(true);

        if (state == 'register'){
            $('#register').click();
        }
        if (state == 'login'){
            $('#log-in').click();
        }
        if (state == 'slider' ){
            $.fancybox.open({src: '#quick-order'});
        }
        if ( state == 'call'){
            $.fancybox.open({src: '#call-order'});
        }
        if ( state == 'bid'){
            $.fancybox.open({src: '#quick-order'});
        }


    });
    
    // мобильная версия
    
    // меню входа в кабинет
    $('#cabinet').on('click', function(event) {
        $('#overlay').remove();
        if (($(event.target).closest("#cabinet-inner").length == 0)){
            $('.mobile #cabinet').toggleClass('visible');
            $('.mobile #main-menu-items, .mobile #product-menu, .mobile #header-middle #search-block, #footer-menu').removeClass('visible');
        }
        if ($('.mobile #cabinet').hasClass('visible')){
            $('<a id="overlay" style="position: absolute; left: 0; top: 0; width:100%; height: 100%; z-index: 15; "></a>').
            appendTo('body');
        }        
        event.stopPropagation();
    });
    
    // строка поиска
    $('#header-middle #search-block').on('click', function(event) {
        if($('#header').hasClass('mobile')){
            $('#overlay').remove();
            if (($(event.target).closest("#search-block form").length == 0)){
                $('.mobile #header-middle #search-block').toggleClass('visible');
                $('.mobile #main-menu-items, .mobile #product-menu, .mobile #cabinet, #footer-menu').removeClass('visible');
            }
            if ($('.mobile #header-middle #search-block').hasClass('visible')){
                $('<a id="overlay" style="position: absolute; left: 0; top: 0; width:100%; height: 100%; z-index: 15; "></a>').appendTo('body');
            }
        }
    });
    
    // мобильное верхнее меню
    $('#main-menu-mobile').on('click', function(event) {
        $('#overlay').remove();
        if ($(event.target).closest("#main-menu-mobile").length != 0){
            $('.mobile #main-menu-items, .mobile #product-menu').toggleClass('visible');
            $('.mobile #cabinet, .mobile #header-middle #search-block, #footer-menu').removeClass('visible');
        } else if (($(event.target).closest("#main-menu-items").length == 0)&&($(event.target).closest("#product-menu").length == 0)){
            $("#main-menu-items").removeClass('visible');
            $("#product-menu").removeClass('visible');
        }
        if ($('.mobile #main-menu-items, .mobile #product-menu').hasClass('visible')){
            $('<a id="overlay" style="position: absolute; left: 0; top: 0; width:100%; height: 100%; z-index: 15; "></a>').appendTo('body');
        }
    });
    
    // мобильное нижнее меню
    $("#footer-menu-mobile").on('click', function(event) {
        $('#overlay').remove();
        if ($(event.target).closest("#footer-menu-mobile").length != 0){
            $('#footer-menu').toggleClass('visible');
            $('.mobile #main-menu-items, .mobile #product-menu, .mobile #cabinet, .mobile #header-middle #search-block').removeClass('visible');            
        } else if (($(event.target).closest("#footer-menu").length == 0)){
            $('#footer-menu').removeClass('visible');
        }
        if ($('#footer-menu').hasClass('visible')){
            $('<a id="overlay" style="position: absolute; left: 0; top: 0; width:100%; height: 100%; z-index: 15; "></a>').appendTo('body');
        }        
    });
    
    $('body').on('click', '#overlay', function(event) {
        $('.mobile #cabinet').removeClass('visible');
        $('.mobile #header-middle #search-block').removeClass('visible');
        $('#main-menu-items').removeClass('visible');
        $('#product-menu').removeClass('visible');
        $('#footer-menu').removeClass('visible');
        $('#city-block').removeClass('visible');
        $('#search-results').removeClass('visible');
        $('#overlay').remove();
    });    
    
    // при изменении размеров окна меняем внешний вид шапки
    $(window).resize(function () {
        if (document.documentElement.clientWidth > 767){
            $('#header').removeClass('mobile');
            $('#footer-menu').removeClass('visible');
        } else {
            $('#header').addClass('mobile');                                
        }
    });

    // Кнопка "Еще" в списке товаров
    $('.product-columns .more-button-container .button').on('click', function() {
        $(this).parent().addClass('hidden');
        $(this).parent().parent().parent().find('.product-container').addClass('visible');
    });

    // Узнать оптовые цены
    $('.category .fancybox-info, .product .get-price, .product-page .get-price').on('click', function(event) {
        $.fancybox.open({src: '#register-modal'});
    });

    $('#message_event').on('click', '.button.close', function(event) {
        $.fancybox.close();
    });


});

// скрываем результаты поиска по клику вне строки поиска и блока с вариантами
$(document).click(function(event) {
    if ($(event.target).closest('#search-block').length == 0 && $(event.target).closest('#search-results-inner').length == 0) {
        $('#search-results').removeClass('visible');
        event.stopPropagation();
    }
});