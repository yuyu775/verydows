//格式化Unix时间戳
function formatTimestamp(time, format) {
  var d = new Date(parseInt(time) * 1000), month = d.getMonth() + 1, day = d.getDate(), hour = d.getHours(), minute = d.getMinutes(), second = d.getSeconds();
  format = format.replace(/y/, d.getFullYear());
  if(month < 10) month = '0' + month;
  format = format.replace(/m/, month);
  if(day < 10) day = '0' + day;
  format = format.replace(/d/, day);
  if(hour < 10) hour = '0' + hour;
  format = format.replace(/h/, hour);
  if(minute < 10) minute = '0' + minute;
  format = format.replace(/i/, minute);
  if(second < 10) second = '0' + second;
  format = format.replace(/s/, second);
  return format;
}

//遮罩层
function masker(s){
  s = s || 'show';
  if(s == 'show'){
    if($('#vds-mask').size() < 1){
      $('<div></div>', {'class':'mask', 'id':'vds-mask'}).css({width: $(window).width(), height: $(window).height()}).appendTo('body');	
    }else{
      $('#vds-mask').show();
    }
  }else{
    $('#vds-mask').hide();
  }
}

//进度条
function loadingBar(s){
  s = s || 'show';
  if(s == 'show'){
    if($('#vds-loading').size() == 0){
      $('<div></div>', {'class':'loading absol', 'id':'vds-loading'}).appendTo($('body')).vdsMidst();
    }else{
      $('#vds-loading').show();
    }
    masker();
  }else{
    masker('hide');
    $('#vds-loading').hide();
  }
}

//随机字符串
function random_chars(length){
  var words = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@%#*_=',
  m = words.length,
  chars = '';
  length = length || 20;
  for (i = 0;i < length;i++) chars += words.charAt(Math.floor(Math.random()*m));
  return chars;
}

/*
 * Verydows Jquery 插件
*/
(function($){
  //表单验证
  $.fn.vdsChecker = function(input_opts, msg_opts, rule_opts){
    var name = typeof(this.attr('title')) == 'undefined' ? '此项' : this.attr('title'),
        val = this.val() || '',
        default_inputs = {
          required: false,
          minlength: false,
          maxlength: false,
          email: false,
          equal: false,
          nonegint: false,
          decimal: false,
        }, input = $.extend(default_inputs, input_opts),
        default_msgs = {
          required: name + '不能为空',
          minlength: name + '长度不能少于' + input.minlength + '个字符',
          maxlength: name + '长度不能大于' + input.maxlength + '个字符',
          email: '无效的邮箱地址',
          equal: name + '不正确',
          nonegint: name + '格式不正确', //非负整数
          decimal: name + '格式不正确',
        }, msg = $.extend(default_msgs, msg_opts),
        default_rule = {
          required: val.length > 0,
          minlength: val.length >= input.minlength,
          maxlength: val.length <= input.maxlength,
          email: /.+@.+\.[a-zA-Z]{2,4}$/.test(val),
          equal: val == input.equal,
          nonegint: /^$|^(0|\+?[1-9][0-9]*)$/.test(val),
          decimal: /^$|^(0|[1-9][0-9]{0,9})(\.[0-9]{1,2})?$/.test(val),
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
    }
			
    if(this.next('span.vds-warning').size() < 1) {$(this).after(warning_html);}
			
    var warning = $(this).next('span.vds-warning'), rs = true;
    $.each(input, function(i){
      if(input[i] && !rule[i]){warning.text(msg[i]); rs = false; return false;} 
    });
    if(rs) warning.remove();
    return this;
  };
	
  //表单提交
  $.fn.vdsSubmit = function(popup){
    //popup = popup || false;
    var err = $(this).find('span.vds-warning');
    if(err.size() > 0){
      if(popup == true){
        var err_msg = '';
        $.each(err, function(i, e){err_msg += $(e).text() + "<br />";});
        $('body').vdsAlert({msg:err_msg, time:2});
      }
      return false;
    }else{
      this.submit();
    }
  }
	
  //横竖居中于窗口
  $.fn.vdsMidst = function(options){
    var defaults = {   
      position: 'fixed', gotop: 0, goleft: 0
    }, opts = $.extend(defaults, options);
		
    this.css({
      position: opts.position, 
      top: ($(window).height() - this.height()) /2 + opts.gotop,
      left: ($(window).width() - this.width()) / 2 + opts.goleft,
    });
    return this;
  }
	
  //提示窗口
  $.fn.vdsAlert = function(options){
    var defaults = {    
      msg: null,
      time: 3,
    }, opts = $.extend(defaults, options);
		
    opts.time = opts.time * 1000;
		
    this.remove('#vds-alert');
    $("<div id='vds-alert'></div>").html(opts.msg).appendTo(this).css({ 
      position: 'absolute',
      width: 300,
      'text-align': 'center',
      top: $(document).scrollTop() + 100,
      left: ($(window).width() - 300) / 2,
      color: '#CC3300',
      'font-size': '14px',
      padding: '30px 20px',
      'line-height': '150%',
      border: '3px solid #ffcc33',
      background: '#fff',
      'box-shadow': '2px 2px 2px #ccc',
      'z-index': 9999
    }).delay(opts.time).fadeOut(1000);
  }
	
  //确认窗口
  $.fn.vdsConfirm = function(options){
    var defaults = {text: '', left: 0, top: 0, confirmed: function(){}}, opts = $.extend(defaults, options), btn = this, obj;

    if($('#vds-confirm').size() == 0){
      var html = "<p class='pad5'>"+opts.text+"</p><div class='mt10'><button type='button' class='ubtn sm btn'>确定</button><span class='sep10'></span><button type='button' class='fbtn sm btn'>取消</button></div>";
      obj = $('<div></div>', {'class':'vds-confirm cut', 'id':'vds-confirm'}).html(html).appendTo($('body'));
    }
    else{
      obj = $('#vds-confirm');
      obj.find('p').html(opts.text);
    }
		
    obj.css({ 
      left: btn.offset().left - obj.width() + opts.left,
      top: btn.offset().top - btn.height() - obj.height() + opts.top,
    }).show().find('button').on('click', function(){
      if($(this).index() == 0) opts.confirmed();
      obj.hide();
    });
  }

  //弹出展示媒体文件窗口
  $.fn.vdsPopMedia = function(options){
    var defaults = {type: 'image', src: null}, opts = $.extend(defaults, options), 
        html = "<a class='close'></a><div class='media'></div>",
        media,
        popup;
	
    if($('#vds-pop-media').size() == 0) popup = $('<div></div>', {'class':'pop-media', 'id':'vds-pop-media'}).html(html).appendTo($('body')); else popup = $('#vds-pop-media');
    switch(opts.type){
      case 'image':
        media = $('<img />', {'src':opts.src,'border':0});
      break;
      case 'flash':
        media = $('<embed></embed>', {
          'src':opts.src,
          'quality':'high',
          'pluginspage':'http://www.macromedia.com/go/getflashplayer',
          'type':application/x-shockwave-flash
        });
      break;
      default: return false;
    }
    popup.hide().find('div.media').empty().append(media);
    media.load(function(){
      popup.css({ 
        left: ($(window).width() - popup.width()) / 2,
        top: ($(window).height() - popup.height()) /2,
      }).show();
    });
		
    //关闭
    popup.find('a.close').on('click', function(){
      $(this).closest('#vds-pop-media').hide();
    });
  }
	
  //行变换class
  $.fn.vdsRowHover = function(cls){
    cls = cls || 'hover';
    this.hover(function(){$(this).addClass(cls);}, function(){$(this).removeClass(cls);}); 
  }
	
  //选项卡切换
  $.fn.vdsTabsSwitch = function(options){
    var defaults = {sw: 'li', maps: '.swcon'}, opts = $.extend(defaults, options);
    this.find(opts.sw).click(function(){
      var i = $(this).index();
      $(this).addClass('cur').siblings().removeClass('cur');
      $(opts.maps).hide().eq(i).show();
    });
  }
	
})(jQuery);