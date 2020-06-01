import 'slick-carousel'
import $ from "jquery";

$(() => {
    $('.app-slider').each((index, element) => {
        const $el = $(element);

        $el.slick({
            slidesToShow: Math.min(3, $el.children().length),
            infinite: true,
            centerMode: true,
            autoplay: true,
            autoplaySpeed: 10000,
            prevArrow: '<span class="h4 pr-1 pr-lg-3"><span class="fa fa-arrow-circle-left"></span></span>',
            nextArrow: '<span class="h4 pl-1 pl-lg-3"><span class="fa fa-arrow-circle-right"></span></span>',
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
                        centerPadding: '0'
                    }
                }
            ]
        });
    });
})