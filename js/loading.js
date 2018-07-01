function showLoading() {
	var xHight = $(window).height();
	var xWidth = $(window).width();
	var loading = $("<div id='loading' class='loading'></div>");
	loading.css("height", xHight + "px");
	loading.css("width", xWidth + "px");
	$('body').append(loading);
}
function hideLoading() {
	$("#loading").remove();
}

function getQueryString(name) {
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
	var r = window.location.search.substr(1).match(reg);
	if (r != null)
		return unescape(r[2]);
	return null;
}
/**
 * 
 * 数字格式转换成千分位
 * 
 * @param{Object}num
 * 
 */

function number_format(num) {
	if ((num + "").trim() == "") {
		return "";
	}
	if (isNaN(num)) {
		return "";
	}
	num = num + "";
	if (/^.*\..*$/.test(num)) {
		var pointIndex = num.lastIndexOf(".");
		var intPart = num.substring(0, pointIndex);
		var pointPart = num.substring(pointIndex + 1, num.length);
		intPart = intPart + "";
		var re = /(-?\d+)(\d{3})/
		while (re.test(intPart)) {
			intPart = intPart.replace(re, "$1,$2")
		}
		num = intPart + "." + pointPart;
	} else {
		num = num + "";
		var re = /(-?\d+)(\d{3})/
		while (re.test(num)) {
			num = num.replace(re, "$1,$2")
		}
	}
	return num;
}

/**
 * “Ctrl+Enter”键提交
 */
$.fn.ctrlEnter = function (btns, fn) { 
    var thiz = $(this); 
    btns = $(btns); 
        
    function performAction (e) { 
        fn.call(thiz, e); 
    }; 
    thiz.bind("keydown", function (e) { 
       if (e.keyCode === 13 && e.ctrlKey) { 
           performAction(e); 
           e.preventDefault(); //阻止默认回车换行 
       } 
    }); 
    btns.bind("click", performAction); 
} 

/**
 * 定义date格式
 * @param fmt
 * @returns
 */
Date.prototype.format = function (fmt) { //author: meizz 
    var o = {
        "M+": this.getMonth() + 1, //月份 
        "d+": this.getDate(), //日 
        "H+": this.getHours(), //小时 
        "m+": this.getMinutes(), //分 
        "s+": this.getSeconds(), //秒 
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度 
        "S": this.getMilliseconds() //毫秒 
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
    if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}