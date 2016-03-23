$(function(){
  var ph = $('body').height() - $('#header').outerHeight() - $('#footer').outerHeight() - 10;
  $('#nav').height(ph);
  $('#main').height(ph);
  $('#nav h3').click(function(){
    if($(this).hasClass('on')) $(this).removeClass('on').next('ul').slideUp(); else $(this).addClass('on').next('ul').slideDown();
  });
  $('#nav li').click(function(){
    $('#nav li.on').removeClass('on');
    $(this).addClass('on');
  });
})
