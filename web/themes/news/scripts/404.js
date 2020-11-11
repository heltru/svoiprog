$(document).ready(function () {
    
    // Кнопка "Еще" в списке товаров
    $('.product-columns .button-container .button').on('click', function() {
        $(this).parent().addClass('hidden');
        $(this).parent().parent().find('.product').addClass('visible');
    });
    
    // мобильное нижнее меню
    $("#footer-menu-mobile").on('click', function(event) {
        $('#overlay').remove();
        if ($(event.target).closest("#footer-menu-mobile").length != 0){
            $('#footer-menu').toggleClass('visible');                       
        } else if (($(event.target).closest("#footer-menu").length == 0)){
            $('#footer-menu').removeClass('visible');
        }
        if ($('#footer-menu').hasClass('visible')){
            $('<a id="overlay" style="position: absolute; left: 0; top: 0; width:100%; height: 100%; z-index: 15; "></a>').appendTo('body');
        }        
    });
    
    $('body').on('click', '#overlay', function(event) {
        $('#footer-menu').removeClass('visible');
        $('#overlay').remove();
    });
    
    // при изменении размеров окна меняем внешний вид шапки
    $(window).resize(function () {
        if (document.documentElement.clientWidth > 767){
            $('#footer-menu').removeClass('visible');
        }
    });    
});