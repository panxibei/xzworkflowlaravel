/**
 * 这里是公共js函数调用库(2014/06/21)
 * 
 */
function clickme(){
	alert("aaaaaa");
}


/**
 * JS关于Date函数的格式化输出
 * @param  {fmt} format    格式
 * @return {string}        格式化的时间字符串
 * var time1 = new Date().Format("yyyy-MM-dd");
 * var time2 = new Date().Format("yyyy-MM-dd HH:mm:ss"); 
 */
Date.prototype.Format = function(fmt){
	var o = {
		 "M+": this.getMonth()+1,
		 "d+": this.getDate(),
		 "H+": this.getHours(),
		 "m+": this.getMinutes(),
		 "s+": this.getSeconds(),
		 "S+": this.getMilliseconds()
	};
 
	//因位date.getFullYear()出来的结果是number类型的,所以为了让结果变成字符串型，下面有两种方法：
 
	if(/(y+)/.test(fmt)){
		//第一种：利用字符串连接符“+”给date.getFullYear()+""，加一个空字符串便可以将number类型转换成字符串。
 
		fmt=fmt.replace(RegExp.$1,(this.getFullYear()+"").substr(4-RegExp.$1.length));
	}
	for(var k in o){
		if (new RegExp("(" + k +")").test(fmt)){
 
			//第二种：使用String()类型进行强制数据类型转换String(date.getFullYear())，这种更容易理解。
 
			fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(String(o[k]).length)));
		}
	}	
	return fmt;
}



/**
 * 和PHP一样的时间戳格式化函数
 * @param  {string} format    格式
 * @param  {int}    timestamp 要格式化的时间 默认为当前时间
 * @return {string}           格式化的时间字符串
 */
function dateFormat(format, timestamp){ 
	// var a, jsdate=((timestamp) ? new Date(timestamp*1000) : new Date());
	var a, jsdate=((timestamp) ? new Date(timestamp*1000) : new Date(0));
	var pad = function(n, c){
		if((n = n + "").length < c){
			return new Array(++c - n.length).join("0") + n;
		} else {
			return n;
		}
	};
	var txt_weekdays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
	var txt_ordin = {1:"st", 2:"nd", 3:"rd", 21:"st", 22:"nd", 23:"rd", 31:"st"};
	var txt_months = ["", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]; 
	var f = {
		// Day
		d: function(){return pad(f.j(), 2)},
		D: function(){return f.l().substr(0,3)},
		j: function(){return jsdate.getDate()},
		l: function(){return txt_weekdays[f.w()]},
		N: function(){return f.w() + 1},
		S: function(){return txt_ordin[f.j()] ? txt_ordin[f.j()] : 'th'},
		w: function(){return jsdate.getDay()},
		z: function(){return (jsdate - new Date(jsdate.getFullYear() + "/1/1")) / 864e5 >> 0},
		// Week
		W: function(){
			var a = f.z(), b = 364 + f.L() - a;
			var nd2, nd = (new Date(jsdate.getFullYear() + "/1/1").getDay() || 7) - 1;
			if(b <= 2 && ((jsdate.getDay() || 7) - 1) <= 2 - b){
				return 1;
			} else{
				if(a <= 2 && nd >= 4 && a >= (6 - nd)){
					nd2 = new Date(jsdate.getFullYear() - 1 + "/12/31");
					return date("W", Math.round(nd2.getTime()/1000));
				} else{
					return (1 + (nd <= 3 ? ((a + nd) / 7) : (a - (7 - nd)) / 7) >> 0);
				}
			}
		},
		// Month
		F: function(){return txt_months[f.n()]},
		m: function(){return pad(f.n(), 2)},
		M: function(){return f.F().substr(0,3)},
		n: function(){return jsdate.getMonth() + 1},
		t: function(){
			var n;
			if( (n = jsdate.getMonth() + 1) == 2 ){
				return 28 + f.L();
			} else{
				if( n & 1 && n < 8 || !(n & 1) && n > 7 ){
					return 31;
				} else{
					return 30;
				}
			}
		},
		// Year
		L: function(){var y = f.Y();return (!(y & 3) && (y % 1e2 || !(y % 4e2))) ? 1 : 0},
		//o not supported yet
		Y: function(){return jsdate.getFullYear()},
		y: function(){return (jsdate.getFullYear() + "").slice(2)},
		// Time
		a: function(){return jsdate.getHours() > 11 ? "pm" : "am"},
		A: function(){return f.a().toUpperCase()},
		B: function(){
			// peter paul koch:
			var off = (jsdate.getTimezoneOffset() + 60)*60;
			var theSeconds = (jsdate.getHours() * 3600) + (jsdate.getMinutes() * 60) + jsdate.getSeconds() + off;
			var beat = Math.floor(theSeconds/86.4);
			if (beat > 1000) beat -= 1000;
			if (beat < 0) beat += 1000;
			if ((String(beat)).length == 1) beat = "00"+beat;
			if ((String(beat)).length == 2) beat = "0"+beat;
			return beat;
		},
		g: function(){return jsdate.getHours() % 12 || 12},
		G: function(){return jsdate.getHours()},
		h: function(){return pad(f.g(), 2)},
		H: function(){return pad(jsdate.getHours(), 2)},
		i: function(){return pad(jsdate.getMinutes(), 2)},
		s: function(){return pad(jsdate.getSeconds(), 2)},
		//u not supported yet
		// Timezone
		//e not supported yet
		//I not supported yet
		O: function(){
			var t = pad(Math.abs(jsdate.getTimezoneOffset()/60*100), 4);
			if (jsdate.getTimezoneOffset() > 0) t = "-" + t; else t = "+" + t;
			return t;
		},
		P: function(){var O = f.O();return (O.substr(0, 3) + ":" + O.substr(3, 2))},
		//T not supported yet
		//Z not supported yet
		// Full Date/Time
		c: function(){return f.Y() + "-" + f.m() + "-" + f.d() + "T" + f.h() + ":" + f.i() + ":" + f.s() + f.P()},
		//r not supported yet
		U: function(){return Math.round(jsdate.getTime()/1000)}
	};
	return format.replace(/[\\]?([a-zA-Z])/g, function(t, s){
		if( t!=s ){
			// escaped
			ret = s;
		} else if( f[s] ){
			// a date function exists
			ret = f[s]();
		} else{
			// nothing special
			ret = s;
		}
		return ret;
	});
}

/**
 * AD的filetime时间戳格式化函数
 * @param  {string} filetime  活动目录的时间戳格式（如：130639104000000000, 2014/12/25 上午12:00:00）
 * @param  {string} format（UnixTime or HumanDate） 需要的格式 默认为人类理解的时间格式
 * @return {string}           格式化的时间字符串
 */
function LdapToEpoch(filetime, format){
	var ldap = filetime;
	var sec = Math.round(ldap/10000000);
	sec -= 11644473600;
	// var datum = new Date(sec*1000);
	// var outputtext="<b>Epoch/Unix time</b>: "+sec; //Unix time
	// outputtext+="<br/><b>GMT</b>: "+datum.toGMTString()+"<br/><b>Your time zone</b>: "+datum.toLocaleString();
	// var outputtext=sec; //Unix time
	// outputtext+=datum.toLocaleString();
	if (format == 'UnixTime'){
		var outputtext = sec; //Unix time
	} else {
		var datum = new Date(sec*1000);
		var outputtext = datum.toLocaleString();
	}
	return outputtext;
}

/**
 * //自动加载，每10秒判断是否session过期。运用到了cookie来判断。
 *
 */
 /* laravel 暂时关闭这个功能。
$(function(){
	// var Home_Index_logout = $.cookie('cookie_mt_Home_Index_logout');
	// alert(typeof($.cookie(cookie_prefix + 'login_ok')));
	
	var timer1 = setInterval(function(){
		//alert($.cookie('cookie_mt_user_token'));
		//alert(typeof($.cookie('cookie_mt_user_token')));
		
		if($.cookie('cookie_wf_rememberme')=='1'){
			//var url = "{:U('Home/Index/check_login_for_cookies')}";
			//$.get(url,{user_token:$.cookie('cookie_mt_user_token')},function(jdata){});
			//alert('aaa');
			return true;
		}
		
		if(typeof($.cookie('cookie_wf_login_ok'))=='undefined'){
			// $.cookie('cookie_mt_login_ok', null, {path:'/'});
			clearInterval(timer1);//取消setInterval，防止跳出两个alert提示
			// alert('会话超时，请重新登录！');
			
			var cookie_wf_Home_Index_logout = $.cookie('cookie_wf_Home_Index_logout')
			BootstrapDialog.show({
				closable: true,
				closeByBackdrop: false,
				closeByKeyboard: false,
				message:'会话超时，请重新登录！',
				buttons: [{
					label: 'Close',
					action: function(dialogRef){
					window.location.replace(cookie_wf_Home_Index_logout);
                    dialogRef.close();
                }}],
				onshow: function(dialogRef){
					dialogRef.getModalContent().css('margin-top',function(){
						var modalHeight = dialogRef.getModalContent().height();
						return ((window.screen.height / 2) - (modalHeight / 2) - 200);
					});
				},
				onhidden: function(dialogRef){
					window.location.replace(cookie_wf_Home_Index_logout);
			}});
			return false;
		}
	},5000);

});
*/


/**
 * 在已知参数名的情况下，获取参数值，使用正则表达式能很容易做到。
 *
 */
function getValue(url, name) {
	var reg = new RegExp('(\\?|&)' + name + '=([^&?]*)', 'i');
	var arr = url.match(reg);
	if (arr) {
		return arr[2];
	}
	return null;
}


/**
 * 如果想获取所有的参数名和其对应的值，同样也可以使用正则表达式的方法
 *
 */
function getKeyValue(url) {
	var result = {};
	var reg = new RegExp('([\\?|&])(.+?)=([^&?]*)', 'ig');
	var arr = reg.exec(url);
	while (arr) {
		result[arr[2]] = arr[3];
		arr = reg.exec(url);
	}
	return result;
}




/**
 * 注意：以下均没有用到。
 */
