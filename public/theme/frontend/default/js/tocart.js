$(function(){
  //商品可选项选择
  $('dd.opt a').click(function(){
    $(this).siblings('.cur').removeClass('cur').remove('i');
    $(this).addClass('cur').append("<i class=\"icon\"></i>").parent().data('checked', $(this).data('key')).parent().removeClass('warning');
    var added_price = 0;
    var now_price = $('#nowprice').data('price');
    $('dd.opt a.cur').each(function(i) {added_price += +$(this).data('price')});	
    now_price = parseFloat(Number(now_price) + Number(added_price)).toFixed(2);
    $('#nowprice').text(now_price);
  });
});

//加入购物车
function addToCart(goods, btn){
  var opt_group = $('dd.opt'), opts_val = [];
  if(opt_group.size() > 0){
    opt_group.each(function(i, e){
      if($(e).data('checked') == '') $(e).parent().addClass('warning'); else opts_val[i] = $(e).data('checked');
    });
  }
  if($('dl.warning').size() > 0) return false; //检查是否有需要选择的商品选项
  if($('#buy-qty font.warning').size() > 0) return false; //检查是否超过库存限制
	
  $.ajax({
    type: "post",
    dataType: "text",
    url: hostUrl+"/index.php?c=order&a=cart&step=add",
    data: {'id':goods, 'qty':$("input[name='qty']").val(), 'opts':opts_val},
    beforeSend:function(){
      $('#tocart-loading').css({ 
        left: $(btn).offset().left,
        top: $(btn).offset().top - $('#tocart-loading').height() - 30,
      }).show();
    },	
    success: function(data){
      $('#tocart-loading').hide();
      $('#tocart-dialog').css({ 
        left: $(btn).offset().left,
        top: $(btn).offset().top - $('#tocart-dialog').height() - 50,
      }).show();
			
      if(data == 1){
        viewCartbar();
        $('#tocart-dialog p').removeClass('err');
        $('#tocart-dialog p font').text('加入购物车成功！');	
      }else if(data == 0){
        $('#tocart-dialog p').addClass('err');
        $('#tocart-dialog p font').text('购物车中已存在此商品！');
      }else{
        $('#tocart-dialog p').addClass('err');
        $('#tocart-dialog p font').text('加入购物车失败！');
      }
    },
    error:function(){$('#tocart-loading').hide();alert('请求出错！')}
  });
}

function toBuy(){
  var opt_group = $('dd.opt'), form = $('#buy-form');
  if(opt_group.size() > 0){
    form.find('input[name^="opts"]').remove();
    var opt_input = '';
    opt_group.each(function(i, e){
      opt_input = '<input type="hidden" name="opts[]" value="'+$(e).data("checked")+'" />';
      if($(e).data('checked') == '') $(e).parent().addClass('warning'); else form.append(opt_input);
    });
  }
  if($('dl.warning').size() == 0) form.submit(); return false;
}

function cancelTocartDialog(){
  $('#tocart-dialog').hide();
}