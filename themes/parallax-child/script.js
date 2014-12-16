
(function($) {
    $('#faqs h5').each(function() {
    var tis = $(this), state = false, answer = tis.next('div').slideUp();
    tis.click(function() {
      state = !state;
      answer.slideToggle(state);
      tis.toggleClass('active',state);
    });
  });
})(jQuery);