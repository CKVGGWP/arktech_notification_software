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
    
    });
  });
})(jQuery);
