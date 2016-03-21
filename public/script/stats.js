$(function(){
  $.ajax({
    url: 'http://pv.sohu.com/cityjson?ie=utf-8',
    dataType: 'script',
    success: function(){callStats(returnCitySN.cip, returnCitySN.cname);},
    error: function(){callStats(0, 0);}
  });
});

function callStats(ip, area){
  if(typeof(hostUrl) == 'undefined') hostUrl = getRequestHost();
  var referrer = parseHost(document.referrer), platform = getPlatform(), browser = getBrowser();
  $.ajax({
    type: 'post',
    dataType: 'json',
    url: hostUrl+'/index.php?m=api&c=common&a=stats',
    data: {'ip':ip, 'area':area, 'referrer':referrer, 'platform':platform, 'browser':browser},
  });
}

function getPlatform(){
  var agent = window.navigator.userAgent, platform = 0;
  if(agent.match(/windows|win32/i)) platform = 1;
  else if(agent.match(/macintosh|mac os x/i)) platform = 2;
  else if(agent.match(/linux/i)) platform = 3;
  return platform;
}

function getBrowser(){
  var agent = window.navigator.userAgent, browser = 0;
  if((agent.match(/msie/i) && !agent.match(/opera/i)) || agent.match(/trident/i)) browser = 1;
  else if(agent.match(/chrome/i)) browser = 2;
  else if(agent.match(/firefox/i)) browser = 3;
  else if(agent.match(/safari/i)) browser = 4;
  else if(agent.match(/opera/i)) browser = 5;
  return browser;
}

function parseHost(url){
  if(typeof(url) == 'undefined' || null == url) return '';
  var matches = url.match(/.*\:\/\/([^\/]*).*/);
  if(matches != null) return matches[1]; else return '';
}

function getRequestHost(){
  var host = window.location.protocol + "//" + window.location.host;
  $.ajax({
    url: host+'/index.php?m=api&c=common&a=jstry',
    dataType: 'text',
    async: false,
    error : function(){
      var pathName = window.location.pathname.substring(1);   
      var appName = pathName == '' ? '' : pathName.substring(0, pathName.indexOf('/'));
      host = host + "/" + appName;
    }
  });
  return host;
}