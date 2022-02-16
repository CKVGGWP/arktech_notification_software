!(function ($) {
  "use strict";

  $(document).ready(function () {
    $(".multiple-slides").slick({
      slidesToShow: 4,
      slidesToScroll: 3,
      dots: false,
      arrows: true,
      draggable: false,
      nextArrow: ".slider-btn-two .next-two",
      prevArrow: ".slider-btn-two .prev-two",
      responsive: [
        {
          breakpoint: 1280,
          settings: {
            slidesToShow: 3,
            slidesToScroll: 2,
          },
        },
        {
          breakpoint: 1024,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 2,
          },
        },
        {
          breakpoint: 768,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
          },
        },
      ],
    });
  });
})(jQuery);
