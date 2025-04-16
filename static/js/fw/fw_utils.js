
var array_keys = function(obj) {
	try{
		ret = Object.keys(obj);
	} catch(e) {
		var i, ret = [];
		for (i in obj) {
			ret.push(i);
		}
	}
	return ret;
}

var is_empty = function(mixedVar, zero_is_not_empty) {
	var undef, key, i, len;

	if (typeof mixedVar === 'object') {
		for (key in mixedVar) {
			if (mixedVar.hasOwnProperty(key)) {
				return false;
			}
		}
		return true;
	}
	if (zero_is_not_empty) {
		var emptyValues = [undef, null, false, ''];
	} else {
		var emptyValues = [undef, null, false, 0, '', '0'];
	}
	for (i = 0, len = emptyValues.length; i < len; i++) {
		if (mixedVar === emptyValues[i]) {
			return true;
		}
	}
	return false;
}

var randInt = function (min, max) 
{
  min = Math.ceil(min);
  max = Math.floor(max);
  return Math.floor(Math.random() * (max - min +1)) + min; 
}

function range(size, startAt) {
	startAt = startAt ? startAt : 0;
	var arr = array_keys(Array(size));
	arr = arr.map(function(i) {return i + startAt});
	if (arr.length == 0) {
		var i = 0, arr = [];
		while (i < size) {
			arr.push(i + startAt);
			i++;
		}
	}
    return arr;
}

var chr = function(s) 
{
	return String.fromCharCode(s);
}

var query_to_object = function(query) 
{
	var i, ret = {}, tmp;
	
	if (query.indexOf('?') !== -1) 
		query = query.split('?')[1];
	query = query.split('#')[0];
	query = query.split('&');
	
	for (i in query) {
		tmp = query[i].split('=');
		if (/\[\]$/.test(tmp[0])) {
			tmp[0] = tmp[0].replace('[]', '');
			if (!ret[tmp[0]]) 
				ret[tmp[0]] = [];
			ret[tmp[0]].push(decodeURIComponent(tmp[1]));
		} else if (/\[([^\]]+)\]$/.test(tmp[0])) {
			tmp[0] = tmp[0].replace(']', '');
			tmp[0] = tmp[0].split('[');
			if (!ret[tmp[0][0]])
				ret[tmp[0][0]] = {};
			ret[tmp[0][0]][tmp[0][1]] = decodeURIComponent(tmp[1]);
		} else {
			ret[tmp[0]] = decodeURIComponent(tmp[1]);
		}
	}
	return ret;
}

var object_to_query = function(obj) 
{
	var i, i2, ret = [];
	for (i in obj) {
		if (obj[i] instanceof Array) {
			for (i2 in obj[i]) {
				ret.push(i+'[]='+encodeURIComponent(obj[i][i2]));
			}
		} else if (typeof obj[i] == 'object') {
			for (i2 in obj[i]) {
				ret.push(i+'['+i2+']='+encodeURIComponent(obj[i][i2]));
			}
		} else {
			ret.push(i+'='+encodeURIComponent(obj[i]+''));
		}
	}
	return ret.join('&');
}

var set_cookie = function (cname, cvalue, exdays) {
	var expires = '';
	if (typeof exdays != 'undefined') {
		var d = new Date();
		d.setTime(d.getTime() + (exdays*24*60*60*1000));
		expires = ";expires="+ d.toUTCString();
	}
	document.cookie = cname + "=" + cvalue + expires + ";path=/;domain=."+WEB_URL+";samesite=Strict;";
}


var delete_cookie = function (cname) {
	document.cookie = cname + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;domain=."+WEB_URL+";samesite=Strict;";
}

var get_cookie = function (cname) {
  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(';');
  for(var i = 0; i <ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

var html_special_chars = function (text) {
  if (typeof text != 'string') {
	return text;
  }
  var map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };

  return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

var nl2br = function(text) {
	return text.replace(new RegExp("\n",'g'), '<br>');
}