function initAreaSelect(){
  $('#province').empty();
  getAreaSelect('province');
  $('#city').empty().append('<option value="">选择城市</option>');
  $('#borough').empty().append('<option value="">选择区/县</option>');
}

function getAreaSelect(id, args, selected){
  selected = selected || ''; args = args || '';
  var url = hostUrl+'/index.php?m=api&c=common&a=area' + args;
	
  $.getJSON(url, function(data){
    var opts = "<option value=''>"+$('#'+id).attr('title')+"</option>";
    for(i in data){
      if(selected == i) opts += "<option value='"+i+"' selected='selected'>"+data[i]+"</option>"; else opts += "<option value='"+i+"'>"+data[i]+"</option>";
    }
    $('#'+id).empty().append(opts);
  });
}

function showConsigneeForm(container, action){
  $('#'+container).slideDown().find('form').data('action', action).find('span.vds-warning').remove();
  $('#new-csg-btn').hide();
}

//取消收件人地址表单
function cancelConsigneeForm(container, addbtn){
  $('#'+container).slideUp().find('form')[0].reset();
  $('#'+addbtn).show();
}

function getConsigneeInfo(container, id){
  var form = $('#'+container).find('form');
  form.find('input[name="id"]').val(id);
  $.getJSON(hostUrl+"/index.php?c=consignee&a=info&id="+id, function(rs){
    if(rs.status == 'success'){
      form.find('input[name="name"]').val(rs.data.name);
      form.find('select[name="province"]').val(rs.data.province);
      form.find('select[name="city"]').val(function(){
        var args = '&province='+rs.data.province;
        getAreaSelect('city', args, rs.data.city);
      });
      form.find('select[name="borough"]').val(function(){
        var args = "&province="+rs.data.province+"&city="+rs.data.city;
        getAreaSelect('borough', args, rs.data.borough);
      });
      form.find('input[name="address"]').val(rs.data.address);
      form.find('input[name="zip"]').val(rs.data.zip);
      form.find('input[name="mobile_no"]').val(rs.data.mobile_no);
      form.find('input[name="tel_no"]').val(rs.data.tel_no);
      form.find('input[name="id"]').val(id);
      showConsigneeForm(container, 'edit');
    }
    else{
      alert(rs.data);
    }
  });
}

function checkConsigneeForm(container){
  var form = $('#'+container),
      province = form.find('select[name="province"]'),
      city = form.find('select[name="city"]'),
      borough = form.find('select[name="borough"]'),
      mobile_no = form.find('input[name="mobile_no"]');
  //验证数据
  form.find('input[name="name"]').vdsChecker({required:true, maxlength:60});
  borough.vdsChecker(
    {required:true},
    {required:'请选择完整的所在地区'},
    {required: province.val() != '' && city.val() != '' && borough.val() != ''}																				  );
  form.find('input[name="address"]').vdsChecker({required:true});
  form.find('input[name="zip"]').vdsChecker({maxlength:6});
  mobile_no.vdsChecker(
    {required:true, format:true},
    {required:'手机或固定电话至少填写一项', format:'手机号码格式不正确'},
    {required: mobile_no.val() != '' || form.find('input[name="tel_no"]').val() != '', format: /^$|^1[3|4|5|8]\d{9}$/.test(mobile_no.val())}
  );
  return form.vdsSubmit(false);
}