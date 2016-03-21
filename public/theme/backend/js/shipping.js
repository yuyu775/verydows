$(function(){
  changeChargeType();
  $('#popscope').vdsMidst({wrapper:$(window)});
});

function popScope(){
  $('#popscope').show();
  masker();
  checkAreaSelected();
}

function closeScope(){
  $('popscope').find('span.vds-warning').remove();
  $('#popscope').hide().find('form')[0].reset();
  masker('hide');
}

function changeChargeType(){
  var type = $('#chargetypesel').val();
  $('div#charge-'+type).find('span.vds-warning').remove();
  $('#charge-box').find('div#charge-'+type).show().siblings('div').hide();
}

function saveScope(){
  var type = $('#chargetypesel').val(), scopetpl = $('#scoperow-tpl').html(), html = '', params = {};
  params.type = type;
  params.area = [];
  //选择的地区
  var area_str = '';
  if(!$('#nationwide').prop('checked')){
    if($('#areasel input:checked').size() > 0){
      $('#areasel label').each(function(i){
        if($(this).find('input').prop('checked')){
          area_str += '<font>'+$(this).text()+'</font>';
          params.area.push($(this).find('input').val());
        }
      });
    }else{
      $('body').vdsAlert({msg:'请至少勾选一个地区'}); return false;
    }
  }else{
    area_str = "<font>全国范围</font>";
    params.area = '0';
  }
  //计费参数
  var chdiv = $('#charge-'+type), charge_str = '', err = 0;
  chdiv.find('input').each(function(i, e){
    if(type != 'piece'){
      $(e).vdsChecker({required:true, decimal:true}, {decimal:'无效的数值'});
    } else {
      $(e).vdsChecker({required:true, nonegint:true}, {nonegint:'无效的数值'});
    }
    if(chdiv.find('span.vds-warning').size() == 0){
      charge_str += '<span>'+$(e).prev().text()+'<font>'+e.value+'</font>'+$(e).next().text()+'</span>';
      params[$(e).data('key')] = e.value;
    } else {
      err ++;
    }
  });
  if(err == 0){
    //赋值给模板
    html = scopetpl.replace('{$area}', area_str).replace('{$charge}', charge_str);
    $(html).data('params', params).appendTo('#scopeli');
    closeScope();
    checkAreaSelected();
  }
}

function checkAreaSelected(){
  if($('#scopeli dt font').size() > 0){
    $('#nationwide').prop('disabled',true).next().addClass('caaa');
    if($('#scopeli dt font').text() == '全国范围'){
      $('#scopebtn').prop('disabled',true).addClass('disabled');
    } else {
      $('#scopebtn').prop('disabled',false).removeClass('disabled');
      $('#areasel label').removeClass('caaa');
      $('#scopeli dt font').each(function(i, s){
      $('#areasel font').each(function(i, a){
        if($(s).text() == $(a).text()){
          $(a).parent().addClass('caaa').find('input').prop('disabled',true);
        }
      });
    });
  }
  } else {
    $('#scopebtn').prop('disabled',false).removeClass('disabled');
    $('#nationwide').prop('disabled',false).next().removeClass('caaa');
    $('#areasel label').removeClass('caaa');
  }
}

function checkWideSelected(){
  if($('#nationwide').prop('checked')){
    $('#areasel').addClass('caaa').find('input').prop('checked', true).prop('disabled',true);
  }else{
    $('#areasel').removeClass('caaa').find('input').prop('checked', false).prop('disabled',false);
  }
}

function removeScope(e){
  $(e).closest('dl').remove();
  checkWideSelected();
  checkAreaSelected();
}