$(function(){
  //商品图片展示
  var each_thumb = $('#thumb-container a'), each_img = $('#goods-imgsrc li'), img_area = $('#goods-imgarea');
  img_area.data('zoom', each_img.eq(0).data('zoom')).zoom({url: img_area.data('zoom')});
  each_thumb.mouseover(function(){
    var i = $(this).index();
    each_thumb.removeClass('cur');
    $(this).addClass('cur');
    img_area.data('zoom', each_img.eq(i).data('zoom'));
    img_area.empty().html(each_img.eq(i).html()).trigger('zoom.destroy').zoom({url: img_area.data('zoom')});
  });
  img_area.hover(
    function(){$(this).siblings('i.zoom').addClass('over');},
    function(){$(this).siblings('i.zoom').removeClass('over');}
  );
	
  //缩略图滚动
  var thumb = $('#thumb-container'), //缩略图容器
      thumb_qty = $('#thumb-container a').size(), //缩略图总数
      forward_btn = $('#tmb-forward-btn'), //前滚按钮
      back_btn = $('#tmb-back-btn'), //后滚按钮
      move_dist = '62px', //每次滚动距离
      move_count = 0; //滚动次数
      
  if(thumb_qty > 5) forward_btn.removeClass('disabled');
  forward_btn.click(function(){
    if((thumb_qty - move_count) > 5){
      back_btn.removeClass('disabled');
      thumb.animate({left: '-='+move_dist}, 300);
      move_count++;
    }else{
      forward_btn.addClass('disabled');
    }
  });
	
  back_btn.click(function(){
    if(move_count > 0){
      forward_btn.removeClass('disabled');
      thumb.animate({left: '+='+move_dist}, 300);
      move_count--;
    }else{
      back_btn.addClass('disabled');
    }
  });
	
  //商品内容选项卡切换
  $('#contabs li').click(function(){
    var i = $(this).index();
    $(this).addClass('cur').siblings('.cur').removeClass('cur');
    $('.content').eq(i).removeClass('hide').siblings('.content').addClass('hide');
  });
  $('.speci table tr:even').addClass('even');
  $('.speci table tr').vdsRowHover({hoverClass:'hover'});
	
  //改变数量
  $('#buy-qty button').click(function(){
    var qty = $(this).siblings('input'), qty_val = parseInt(qty.val());
    qty.parent().find('font.red').remove();
    if($(this).index() == 0){
      if(qty_val > 1) qty.val(qty_val - 1);
    }else{
      var stock = qty.data('stock');
      if(qty.val() < stock){
        qty.val(qty_val + 1);
      }else{
	exceededStock(stock);
      }
    }
  });
  $('#buy-qty input').keyup(function(){
    var qty = $(this).val(), stock = $(this).data('stock');
    if(!/(^[1-9]\d*$)/.test(qty)){
      alert('请输入一个正确格式的购买数量！');
      $(this).focus().val(1);
      return false;
    }else if(qty > stock){
      exceededStock(stock);
      $(this).focus().val(stock);
    }
  });
})

function exceededStock(stock){
  var container = $('#buy-qty');
  container.find('font.warning').remove();
  $("<font class='warning red ml10'></font>").text("此商品最多只能购买 "+stock+" 件").appendTo(container);
}