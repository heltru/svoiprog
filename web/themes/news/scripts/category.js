$(document).ready(function () {
    
    // меню категорий на мобильной версии
    $('#category-menu').on('click', function() {
        $('#categories-list').toggleClass('visible');
    });

    // сортировка выдачи
    $('#sort .sort-option').on('click', function() {
        console.log('click');
        var current_id, active_id;
        active_id = $('#sort .active-sort').attr('id');
        current_id = $(this).attr('id');
        $('#sort .sort-option').removeClass('active-sort');
        $(this).addClass('active-sort');

        // если кликнули по тому же пункту, меняем порядок сортировки
        if (active_id == current_id){
            $(this).toggleClass('sort-up');
        }

        var sort = ($(this).hasClass('sort-up')) ? '' : '-';
        sort += $(this).attr('id');

        var url = $('#sort').data('url');

        var url_id = $('#sort').data('url_id');

        refreshItems(sort,url,$('#sort-bar').data('page'),$('#sort').data('filter'),url_id);

    });

    function refreshItems(id_field ,url ,page , filter, url_id) {
        $.ajax({
            type:"GET",
            url:url,
            data:{idfield:id_field,page:page,filter:filter,url_id:url_id},
            success:function (response) {
                if (typeof response == 'object'){

                    if ( ('status' in response ) &&
                        response.status == 200 &&
                        ('data' in response)) {
                        $('#filter-results').text('');
                        console.log(
                            $( response.data ).filter('#filter-results').html()
                        );
                        $('#filter-results').html( $( response.data ).filter('#filter-results').html() );
                    }
                }
            }
        });
    }

    function saveViewType(view_type) {
        $.ajax({
            type:"POST",
            url:$('#view').data('url'),
            data:{view_type:view_type,
                _csrffe:$('meta[name=csrf-token]').attr('content')
            }
        });
    }
    
    // вид выдачи
    $('#view .view-option').on('click', function() {
        console.log('click');
        $('#view .view-option').removeClass('active-view');
        $(this).addClass('active-view');
        if ($('#view .active-view').attr('id') == 'view-list'){
            $('.category .products .product-container').addClass('list');

            saveViewType('view-list');
        } else {
            $('.category .products .product-container').removeClass('list');
            saveViewType('view-tile');

        }
    });

    // прибавление/убавление количества товара
    $('#filter-results').on('click', '.count-control .remove, .count-control .add', function(e){
        var items = 1,
            input,
            sum = 0;
        input = $(this).siblings('.count');
        if (input.length == 0) {
            return;
        }

        items = parseInt(input[0].value,10) + (e.target.className === 'remove'?-1:1);
        if(items < 1 || isNaN(items)){
            items = 1;
        }
        input[0].value = items;
    });

    // показать/скрыть дополнительную информацию по преимуществу оптовика
    $('#register-modal .feature-title').on('click', function() {
        $(this).parent().find('.additional-info').toggleClass('hidden');
    });

    // включение/выключение пунктов фильтра
    $('#categories-list.filter li').on('click', function() {
        if (!($(this).hasClass('button-container'))){
            if ($(this).parent().find('.checked').length > 0 ){
                $(this).toggleClass('checked');
                $(this).find('input').prop('checked', ! $(this).find('input').prop('checked') );

            }
            if ($(this).parent().find('.checked').length == 0 ){
                $(this).addClass('checked');
                $(this).find('input').prop('checked', ! $(this).find('input').prop('checked') );
            }
        }
    });

    $('#categories-list .button-container .button').on('click', function() {
        $('#filterForm').submit();
    });

});