$(document).ready(function () {

    // преобразование select в ul/li

    var  selects = [];
    $('#content').find('.custom-list').each(function() {
        selects.push($(this));
    });

    selects.forEach(function(item, i, selects) {
        // Элемент select, который будет замещаться:
        var select = item;

        var selectBoxContainer = $('<div>',{
            class	: 'custom-select',
            html		: '<div class="selectBox"></div>'
        });

        var dropDown = $('<ul>',{class:'dropDown'});
        var selectBox = selectBoxContainer.find('.selectBox');

        // Цикл по оригинальному элементу select
        var sel = false;
        select.find('option').each(function(i){
            var option = $(this);


            /*if(i==0){
             selectBox.html(option.text());
             } */

            if (option.prop('selected')){
                selectBox.html(option.text());
                sel = true;
            }



            // Создаем выпадающий пункт в соответствии с данными select:
            var li = $('<li>',{
                html: option.text()
            });

            li.click(function(){

                selectBox.html(option.text());
                dropDown.trigger('hide');

                // Когда происходит событие click, мы также отражаем изменения в оригинальном элементе select:
                select.val(option.val()).change();

                return false;
            });

            dropDown.append(li);
        });

        if (! sel){
            select.find('option').each(function(i){
                if(i==0){
                    selectBox.html(option.text());
                }
            });
        }

        selectBoxContainer.append(dropDown.hide());
        select.hide().after(selectBoxContainer);

        // Привязываем пользовательские события show и hide к элементу dropDown:
        dropDown.bind('show',function(){
            if(dropDown.is(':animated')){
                return false;
            }

            selectBox.addClass('expanded');
            dropDown.slideDown();

        }).bind('hide',function(){

            if(dropDown.is(':animated')){
                return false;
            }

            selectBox.removeClass('expanded');
            dropDown.slideUp();

        }).bind('toggle',function(){
            if(selectBox.hasClass('expanded')){
                dropDown.trigger('hide');
            }
            else dropDown.trigger('show');
        });

        selectBox.click(function(){
            dropDown.trigger('toggle');
            return false;
        });

        // Если нажать кнопку мыши где-нибудь на странице при открытом элементе dropDown, он будет спрятан:
        $(document).click(function(){
            dropDown.trigger('hide');
        });

    });

    $("#volume_select").change(function (e) {
        var idmod = $(this).val();
        $.ajax({
            type:"POST",
            url:"/product/select-mod",
            data:{idmod:idmod,_csrffe:$('meta[name=csrf-token]').attr('content')},
            success:function (data){
                if (typeof data == 'object'){
                    if ( 'status' in data && 'data' in data && 'product' in data.data ){

                        if ('description' in data.data.product){
                            //console.log(data.data.product.description);
                            $("#tab-description").html(data.data.product.description);
                        }
                        if ('curr_price' in data.data.product){
                           // $('.prices .sum').html(data.data.product.curr_price);
                            $('.new-price span[itemprop="price"]').html(data.data.product.curr_price);
                        }
                        if ('price_block' in data.data.product){
                            $('#product-features #discount-block ul').html(data.data.product.price_block);
                        }

                        if ('msg_discount' in data.data.product){
                            $('#product-info .discount-info').html(data.data.product.msg_discount);
                        }

                        $('#product-info .button').attr('data-id',idmod);
                        //tab-description
                    }
                }

            }
        });
       console.log( $(this).val());
    });

    // слайдер продукта
    $('#product-slider-main').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        dots: false,
        fade: true,
        asNavFor: '#product-slider-thumbnails'
    });
    
    $('#product-slider-thumbnails').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        asNavFor: '#product-slider-main',
        arrows: false,
        dots: false,
        focusOnSelect: true
    });
    
    // всплывающее окно с фото продукта из слайдера
    $("#product-slider-main a").fancybox({
        loop: true,
        animationEffect: 'zoom',
        transitionEffect: 'slide',

    });
    
    // прибавление/убавление количества товара
    $('.count-control .remove, .count-control .add').click(function(e){
        var items = 1,
            input,
            sum = 0;
        input = $(this).siblings('.count');
        if (input.lengt == 0) {
            return;
        }
        
        items = parseInt(input[0].value,10) + (e.target.className === 'remove'?-1:1);
        if(items < 1 || isNaN(items)){
            items = 1;
        }
        console.log(items );
        input[0].value = items;        

    });

    function multProductPrice(mult) {
        $('#product-info .prices .sum').text().toString().replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, "$1 ");
    }
    
    // показать/скрыть дополнительную информацию по преимуществу оптовика
    $('#register-modal .feature-title').on('click', function() {
        $(this).parent().find('.additional-info').toggleClass('hidden');
    });
    
    // переключение вкладок
    $('.tab-control').on('click', function() {
        var target;
        $(this).parent().find('.tab-control').removeClass('tab-control-active');
        $(this).addClass('tab-control-active');        
        target = '#' + $(this).data('target');
        $(this).parent().parent().find('.tab-content').removeClass('tab-active');
        $(this).parent().parent().find(target).addClass('tab-active');
        
        // сворачиваем форму для отзыва
        if (target == '#tab-reviews'){
            $('#leave-feedback').addClass('hidden');
            $('#add-review').text('Добавить отзыв');
        }
    });
	
    // кнопка "Добавить отзыв"
    $('#add-review').on('click', function() {
        $('#leave-feedback').toggleClass('hidden');
        if ($('#leave-feedback').hasClass('hidden')){
            $('#add-review').text('Добавить отзыв');
        } else {
            $('#add-review').text('Написать позже');
        }
    })	

	// выставление оценки в отзыве
    $('#rating-feedback label').on('click', function() {
        $(this).find('input').prop('checked', true );
    });    
    
    // наведение на звездочки в отзыве
    $('#rating-feedback label').mouseover(function(){
        var rating;
        rating = $(this).find('input').val();
        $('#rating-feedback label').removeClass('active');
        $('#rating-feedback label').each(function() {
            if($(this).find('input').val() <= rating){
                $(this).addClass('active');
            }
        });
    });
    
    // наведение на звездочки прекратилось, устанавливаем выбранную оценку
    $('#rating-feedback div').mouseleave(function(){
        var rating;
        rating = $('input[name=feedback-rating]:checked').val();
        $('#rating-feedback label').removeClass('active');
        $('#rating-feedback label').each(function() {
            if($(this).find('input').val() <= rating){
                $(this).addClass('active');
            }
        });
    });
	
    // добавление файла
     $("#photo-feedback input").change(function(event) {
        if (this.files[0]){
            $(this).closest("label").find('span').text(this.files[0].name);
            $(this).parent().parent().find('label.hidden').filter( ':first' ).removeClass('hidden');
        }
    });
    // прокрутка колонок в таблице сравнения - далее
    $('.compare-container div').on('click', function() {
        var current_column = 0,
            column_num = 0,
            next_column = 0,
            current_table,
            btn;

        btn = $(this).attr('class'); // нажато далее или назад

        current_table = $(this).parent().find('.compare'); // текущая таблица
        current_column = current_table.find('.table-header').last().find('.visible-cell').index(); // текущая модель
        column_num = current_table.find('.table-header .compare-name').attr('colspan'); // всего моделей

        // какой столбей показать
        if (btn == 'columnn-next') {
            next_column  = current_column + 1;

            if(next_column > column_num - 1){
                next_column = 0;
            }
        } else {
            next_column  = current_column - 1;
            if(next_column == - 1){
                next_column = column_num - 1;
            }
        }

        current_table.find('tr').each(function () {
            $(this).find('.visible-cell').removeClass('visible-cell');
            $(this).find('.mod:eq(' + next_column + ')').addClass('visible-cell');
        });

    });

});