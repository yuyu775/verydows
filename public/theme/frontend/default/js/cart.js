$(function(){
  total_cart();
  //改变数量
  $('.qty button').click(function(){
    var qty = $(this).siblings('input'), qty_val = parseInt(qty.val());
    if($(this).index() == 0){
      if(qty_val > 1) qty.val(qty_val - 1);
    }else{
      var stock = qty.data('stock');
      if(qty.val() < stock){
        qty.val(qty_val + 1);
      }else{
	alert("此商品最多只能购买 "+stock+" 件");
	return false;
      }
    }
    total_cart();
  });
  $('.qty input').keyup(function(){
    var qty = $(this).val(), stock = $(this).data('stock');
    if(!/(^[1-9]\d*$)/.test(qty)){
      alert('请输入一个正确格式的购买数量！');
      $(this).focus().val(1);
    }else if(qty > stock){
      alert("此商品最多只能购买 "+stock+" 件");
      $(this).focus().val(stock);
    }
    total_cart();
  });
  //删除购物车商品
  $('.remove-row').click(function(){
    var row = $(this).closest('tr.cart-row');
    $(this).vdsConfirm({
      text: '您确定要删除此商品吗?',
      left: -35,
      top: -15,
      confirmed: function(){
        $.ajax({
          type: "post",
          dataType: "text",
          url: hostUrl+"/index.php?c=cart&a=index&step=remove",
          data: {'key': row.attr('data-key')},
          beforeSend:function(){$('body').vdsLoading({text:'正在删除...'});},	
          success: function(data){
            $('body').vdsLoading({sw:false});
            if(data == 1){
              row.remove();
              total_cart();
              if($('.cart-row').size() < 1) $('.container').empty().append("<div class='cart-empty cut'><p class='c666'>您的购物车是空的！<a href='"+hostUrl+"'>快去逛一逛</a>，找到您喜欢的商品放进购物车吧。</p></div>");
            }else{
              alert('删除失败，请重试!');
            }
	  },
          error:function(){$('body').vdsLoading({sw:false});alert('请求出错！');}
        });
      },
    });
  });
  //清空购物车
  $('#clear-cart').click(function(){
    $(this).vdsConfirm({
      text: '您确定要清除购物车中全部商品吗?',
      left: 260,
      top: -15,
      confirmed: function(){
        $.ajax({
          type: "post",
          dataType: "text",
          url: hostUrl+"/index.php?c=cart&a=index&step=clear",
          beforeSend:function(){
            $('body').vdsLoading({text:'正在清空您的购物车...'});
          },
          success: function(data){
            $('body').vdsLoading({sw:false});
            if(data == 1){
              $('.container').empty().append("<div class='cart-empty cut'><p class='c666'>您的购物车是空的！<a href='"+hostUrl+"'>快去逛一逛</a>，找到您喜欢的商品放进购物车吧。</p></div>");
            }else{
              alert('清空购物车失败，请重试！');
            }
          },
          error:function(){$('body').vdsLoading({sw:false});alert('请求出错！');}
        });
      },
    });	
  });
});

function total_cart(){
  var total = 0.00, adding = '';
  $('.cart-row').each(function(i, e){
    var unit_val = parseFloat($(e).find('.unit-price').text()),
        qty_val = parseInt($(e).find('.qty').find('input').val()),
        subtotal_val = (unit_val * qty_val).toFixed(2);
		
    $(e).find('.subtotal').text(subtotal_val);
    if(i == $('.cart-row').size() - 1) adding += subtotal_val; else adding += subtotal_val + ' + ';
    total = total + parseFloat(subtotal_val);
  });
  $('#item-count').text($('.cart-row').size());
  $('#subtotal-adding').text(adding);
  $('#total').text(total.toFixed(2)); 
}