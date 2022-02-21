$(document).ready(function() {
  $('.accordeon__block-title').click(function(event) {
    if ($('.accordeon__block').hasClass('one')) {
      $('.accordeon__block-title').not($(this)).removeClass('active');
      $('.accordeon__block-text').not($(this).next()).slideUp(300);
    }
    $(this).toggleClass('active').next().slideToggle(300);
  });
});
