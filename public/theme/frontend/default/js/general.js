var hostUrl = getHostUrl();
var _time = null;
$(function(){
  //顶部用户信息
  viewUserbar();
  //分类导航
  $('#cateth').hover(
    function(){
      clearTimeout(_time);
      _time = setTimeout(function(){
        $('#cateth .catebar').slideDown();
        $('#cateth i').addClass('up');
      }, 300);
      
    },
    function(){
      clearTimeout(_time);
      _time = setTimeout(function(){
        $('#cateth .catebar').slideUp();
        $('#cateth i').removeClass('up');
      }, 500);
    }
  );
  $('#catebar li.haschild').hover(
    function(){$(this).addClass('hover');},
    function(){$(this).removeClass("hover");}
  );
  //加载购物车小窗信息
  viewCartbar();
});

function viewUserbar(){
  $.getJSON(hostUrl+"/index.php?c=user&a=login&step=infobar", function(data){
    if(data.status == 1){
      var container = $('#login-userbar'), html = juicer($('#logininfo-tpl').html(), data.info);
      container.empty().append(html);
      container.find('a.user').on('mouseover', function(){
        $(this).addClass('hover');
        container.find('div.userbar').removeClass('hide');
      });
      container.find('div.userbar').on('mouseleave', function(){
        container.find('a.user').removeClass('hover');
        $(this).addClass('hide');
      });
    }														   
  });
}

function viewCartbar(){
  $.ajax({
    type: 'post',
    url: hostUrl+"/index.php?c=order&a=cart&step=bar",
    beforeSend:function(){$("#cartbar").find('img').removeClass('hide');$("#cartbar").find('font').addClass('hide');},	
    success: function(data){
      $("#cartbar").find('img').addClass('hide').next('font').removeClass('hide').next('b').text(data);
    },
    error:function(){alert('购物车数据错误');}
  });
}

(function($){  
  //表单验证
  $.fn.vdsChecker = function(input_opts, msg_opts, rule_opts) {
    var e = this, val = e.val() || '';
    var name = typeof(e.attr('title')) == 'undefined' ? '此项' : e.attr('title'),
        default_inputs = {
          required: false,
          minlength: false,
          maxlength: false,
          email: false,
          password: false,
          equal: false,
          nonegint: false,
          decimal: false,
        }, input = $.extend(default_inputs, input_opts),

        default_msgs = {
          required: name + '不能为空',
          minlength: name + '不能少于' + input.minlength + '个字符',
          maxlength: name + '不能超过' + input.maxlength + '个字符',
          email: '无效的邮箱地址',
          password: '不符合格式要求',
          equal: name + '不正确',
          nonegint: name + '格式不正确',
          decimal: name + '格式不正确', 
        }, msg = $.extend(default_msgs, msg_opts),
			
        default_rule = {
          required: val.length > 0,
          minlength: val.length >= input.minlength,
          maxlength: val.length <= input.maxlength,
          email: /.+@.+\.[a-zA-Z]{2,4}$/.test(val),
          password: /^[\\~!@#$%^&*()-_=+|{}\[\],.?\/:;\'\"\d\w]{6,31}$/.test(val),
          equal: val == input.equal,
          nonegint: /^$|^(0|\+?[1-9][0-9]*)$/.test(val),
          decimal: /^$|^(0|[1-9][0-9]{0,9})(\.[0-9]{1,2})?$/.test(val), //decimal(10进位长度为2)
        }, rule = $.extend(default_rule, rule_opts),

        warning_html = $("<span class='vds-warning'></span>");
			
    if(this.data('warnpos') == 'fixed'){
      warning_html.css({
        position: 'fixed',
        left: this.offset().left + this.outerWidth() + 5,
        top: this.offset().top - $(document).scrollTop(),
      });
    }else if(this.data('warnpos') == 'br'){
      warning_html.css({display: 'block', 'margin-top': 8});
    }else{
      warning_html.addClass('inline');	
    }
			
    if(this.next('span.vds-warning').size() < 1) {$(this).after(warning_html);}
			
    var warning = $(this).next('span.vds-warning'), rs = true;

    $.each(input, function(i){
      if(input[i] && !rule[i]){warning.text(msg[i]); rs = false; return false;} 
    });

    if(rs) warning.remove();
  };

  $.fn.vdsSubmit = function(submitted){
    if($(this).find('span.vds-warning').size() == 0){
      if(submitted == true) $(this).submit(); else return true;
    }else{
      return false;
    }
  }

  $.fn.vdsDialog = function(options){
    var defaults = {
          type: 'ok', //or err
          title: '提示',
          text: '',
        }, opts = $.extend(defaults, options), dialog;
		
    var html = "<h2>"+opts.title+"</h2><dl><dt class='"+opts.type+"'><i class='icon'></i><font>"+opts.text+"</font></dt><dd><button type='button' class='sm-blue'>确定</button></dd></dl><a class='close'><i class='icon'></i></a>";
		
    if($('#vds-dialog').size() == 0){
      dialog = $('<div></div>', {'class':'vds-dialog', 'id':'vds-dialog'}).html(html).appendTo($('body'));
      dialog.css({ 
        left: ($(window).width() - dialog.width()) / 2,
        top: ($(window).height() - dialog.height()) /2,
      }).show();
    }
    else{
      dialog = $('#vds-dialog');
      dialog.empty().html(html).show();
    }

    dialog.find('.close').on("click", function(){$(this).parent().hide();});
    dialog.find('button').on("click", function(){dialog.hide();});
  }
	
  $.fn.vdsConfirm = function(options){
    var defaults = {
          text: '',
          left: 0,
          top: 0,
          confirmed: function(){},
        }, opts = $.extend(defaults, options), btn = this, sure;

    if($('#vds-sure').size() == 0){
      var html = "<p>"+opts.text+"</p><div class='mt10'><button type='button' class='sm-blue'>确定</button><button type='button' class='sm-gray'>取消</button></div>";
      sure = $('<div></div>', {'class':'vds-sure radius4 cut', 'id':'vds-sure'}).html(html).appendTo($('body'));
    }
    else{
      sure = $('#vds-sure');
      sure.find('p').text(opts.text);
    }
		
    sure.css({ 
      left: btn.offset().left - sure.width() + opts.left,
      top: btn.offset().top - btn.height() - sure.height() + opts.top,
    }).show().find('button').on('click', function(){
      if($(this).index() == 0) opts.confirmed();
      sure.hide();
    });
  }

  $.fn.vdsArrVal = function(){
    var vals = [], obj = $(this);
    if(obj.size() > 0) obj.each(function(i, e){vals[i] = $(e).val()});
    return vals;
  }
  
  $.fn.vdsLoading = function(options){
    var defaults = {
          text: '正在加载, 请稍后...',
          container: 'vds-loading', //容器id
          sw: true //开关
    }, opts = $.extend(defaults, options), loading = $('#'+opts.container);
			
    if(opts.sw == true){
      loading.css({
        top: ($(window).height() - loading.height()) / 2,
        left: ($(window).width() - loading.width()) / 2,
      }).show().find('dt').text(opts.text);
      masker('show');
    }
    else{
      masker('hide');
      loading.hide();
    }	
  }
	
  $.fn.vdsRowHover = function(classname){
    classname = classname || 'hover';
    this.hover(function(){$(this).addClass(classname);}, function(){$(this).removeClass(classname);}); 
  };
	
})(jQuery);

function masker(s){
  if(s == 'show'){
    if($('#vds-mask').size() < 1) $('<div></div>', {'class':'vds-mask', 'id':'vds-mask'}).css({width: $(document).width(), height: $(document).height()}).appendTo('body');
    else $('#vds-mask').show();
  }else{
    $('#vds-mask').hide();
  }
}

function getHostUrl(){
  var host = window.location.protocol + "//" + window.location.host;
  $.ajax({
    url: host+'/index.php?m=api&c=common&a=jstry',
    dataType: 'text',
    async: false,
    error : function(res){
      var pathName = window.location.pathname.substring(1);   
      var appName = pathName == '' ? '' : pathName.substring(0, pathName.indexOf('/'));
      host = host + "/" + appName;
    }
  });
  return host;
}