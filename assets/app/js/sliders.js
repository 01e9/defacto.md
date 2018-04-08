import 'slick-carousel'

jQuery(($) => {
    $('.team-slider').slick({
        slidesToShow: 3,
        infinite: true,
        centerMode: true,
        autoplay: true,
        autoplaySpeed: 10000,
        prevArrow: '<span class="h4 pr-1"><span class="fa fa-arrow-circle-left"></span></span>',
        nextArrow: '<span class="h4 pl-1"><span class="fa fa-arrow-circle-right"></span></span>',
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false,
                    centerPadding: '1em'
                }
            }
        ]
    });
})