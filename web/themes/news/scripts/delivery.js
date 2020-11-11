$(document).ready(function () {
    $('#delivery-slider').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        dots: false,
        fade: true,
        asNavFor: '#delivery-thumbnails'
    });
    
    $('#delivery-thumbnails').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        asNavFor: '#delivery-slider',
        arrows: true,
        dots: false,
        focusOnSelect: true,
        responsive: [
            {
                breakpoint: 980,
                settings: {
                    slidesToShow: 3
                }
            },            
            {
                breakpoint: 640,
                settings: {
                    slidesToShow: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                }
            }
        ]
    });     
});