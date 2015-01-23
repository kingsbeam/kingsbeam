var history_url = new Array();
var cururl = '';
var counter  = 0;
var login_info = {};
var ajaxformOption = {
			beforeSubmit: function(arr, $form, options) {
				return true;
			},
			success: function(ret, xhr, $form) {
				$("#body").html(ret);
			} 
		};
$(function(){
	
	var h = $(window).innerHeight()- 150;
	var w = $('#layout-playground').width();
	$('#layout-playground').height(h);
	/*
	if($('#layout-img').width() > w || $('#layout-img').height() > h){

	}
	$('#layout-playground .layout').draggable({
	  	snap: false,
	  	drag: function( event, ui ) {
	  		if(ui.position.left > 5 ){
	  			ui.position.left = 5;
	  		}else if(ui.position.left < -185){
	  			ui.position.left = -185;
	  		}
	  		if(ui.position.top > 5 ){
	  			ui.position.top = 5;
	  		}else if(ui.position.top < -265){
	  			ui.position.top = -265;
	  		}
	  	}
	});*/
	//$("#layout-playground").smoothTouchScroll();
	$('#layout-playground').perfectScrollbar();
	$('#layout-img').droppable({drop: function( event, ui ) { 
			var o = $('#layout-img').offset();
			var x = ui.offset.left - o.left;
			var y = ui.offset.top - o.top;
			if(ui.draggable.hasClass('ico-mode')){
				chg_location(ui.draggable, x, y);
			}else{
				var el = ui.draggable.clone();
				el.addClass('ico-mode').appendTo('#layout-img').css({ position:"absolute", top: y + 'px', left: x + 'px'}).draggable({ containment:"parent" });
				chg_location(el, x, y);
				ui.draggable.remove();
			}
		}
	});
	var d = new Date();
	var n = d.getDay();
	var ds = d.getFullYear()+ "年" + (d.getMonth()+1) + "月" + d.getDate() + "日";
	$('#weekdaytable td').eq(n).find('a').addClass('today');
	$('#today-weekday').html($('#weekdaytable td').eq(n).find('a').attr('name'));
	$('#today-date').html(ds);
	chg_city(2306179,'台北市' );
	parseRSS('https://news.google.com.tw/news?pz=1&cf=all&ned=tw&hl=zh-TW&output=rss', function(feed){
		$.each(feed.entries, function(k,v){
			var onenews = '<dt><a href="'+v.link+'" target="_blank">'+v.title+'</a></dt><dd>'+v.contentSnippet.substring(0,50)+' ...</dd>';
			$('.news').append(onenews);	
		});
	});
	$('#news').perfectScrollbar();
	$('#sec_selt').change(function(){
		var fid = $(this).val();
		var dr = $(this).find('option:selected').attr('dr');
		chg_freeway(fid, dr);
	});
	chg_freeway('10010', 'ns');
	refresh_devices();
});
function show_ctrl(eptype, devID){
	if(login_info.loginid) return false;
	switch(eptype){
		case "15": //計量插座
			var cont = '<button type="button" class="btn btn-default" onclick="send_ctrl(\''+devID+'\',\'11\');">開</button>';
			cont += ' &nbsp; <button type="button" class="btn btn-default" onclick="send_ctrl(\''+devID+'\',\'10\');">關</button>';
			create_modal('計量插座控制', cont);
		break;
		case "22": //紅外控制

		break;
		case "02": //紅外感應
			var btn1 = '<button type="button" class="btn btn-default" onclick="send_ctrl(\''+devID+'\',\'0\');">撤防</button>';
			var btn2 = ' &nbsp; <button type="button" class="btn btn-default" onclick="send_ctrl(\''+devID+'\',\'1\');">佈防</button>';
			var cont = btn1 + btn2;
			create_modal('紅外感應裝置', cont);
		break;
		case "crestron":
			var ip = $('#' + devID).attr('ip');
			$('#crestron-window').attr('src', ip);
			$('#crestron-modal').modal('show');
		break;
	}
}
function send_ctrl(devID, ctrlcode){
	get_data('_includes/devices.php?cmd=control', {devID:devID, ctrlcode:ctrlcode}, function(res){
		$('#ctrl-modal').modal('hide');
		refresh_devices(true);
	});
}
function create_modal(title, cont){
	$('#ctrl-title').html(title);
	$('#ctrl-content').html(cont);
	$('#ctrl-modal').modal('show');
}
function chg_location(el, x, y){
	var devID = el.attr('dev');
	var location = x + ',' + y;
	get_data("_includes/devices.php?cmd=chg_location", {devID:devID, location:location});
}
function get_devices(){
	$('div.ctl').remove();
	$.get( "_includes/devices.php?cmd=get", function( ret ) {
		var data = $.parseJSON(ret);
		$.each(data, function(k,v){
			var htm = '<div id="'+v.devID+'" eptype="'+v.epType+'" dev="'+v.devID+'" ip="'+v.ip+'" onclick="show_ctrl(\''+v.epType+'\', \''+v.devID+'\')" class="pull-left ctl '+ v.status +'"><i class="fa fa-2x '+ v.icon +'"></i>';
			htm += '<ul><li>' + v.html + '</li></ul></div>';
			var el = $(htm);
			if(v.location){
				el.addClass('ico-mode').css({position:"absolute", top: v.y +'px', left: v.x + 'px'}).appendTo('#layout-img').draggable({ containment:"parent" });
			}else{
				el.appendTo('#layout-elements').draggable({revert: true});
			}
		});
	});
}
function refresh_devices(once){
	counter++;
	$('#refresh_counter').html(counter);
	$.get( "_includes/devices.php?cmd=get", function( ret ) {
		var data = $.parseJSON(ret);
		$.each(data, function(k,v){
			if($('div[dev="'+v.devID+'"]').length > 0){

				$('div[dev="'+v.devID+'"] ul li').html(v.html);
			}else{
				var htm = '<div id="'+v.devID+'" eptype="'+v.epType+'" dev="'+v.devID+'" ip="'+v.ip+'" class="pull-left ctl '+ v.status +'"><i class="fa fa-2x fa-fw '+ v.icon +'"></i>';
				htm += '<ul><li>' + v.html + '</li></ul></div>';
				var el = $(htm);
				if(v.location){
					el.addClass('ico-mode').css({position:"absolute", top: v.y +'px', left: v.x + 'px'}).appendTo('#layout-img').click(function(){
						show_ctrl(v.epType, v.devID);
					}); //.draggable({ containment:"parent" })
				}else{
					el.appendTo('#layout-elements').click(function(){
						show_ctrl(v.epType, v.devID);
					}); //.draggable({revert: true})
				}
			}
		});
		if(!once) setTimeout(refresh_devices, 1500);
	});
}
function chg_freeway(fid, dr){
	$('#traffics_table tbody').html('<tr class="reading"><td colspan="3"><div class="text-center" style="height:250px">讀取中...</div></td>');
	if(dr == 'ew'){
		$('#dr-s').html('東向');
		$('#dr-n').html('西向');
	}else{
		$('#dr-s').html('南向');
		$('#dr-n').html('北向');
	}
	$.get( "_includes/traffic.php?fid=" + fid, function( ret ) {
		var row = '';
		var data = $.parseJSON(ret);
		$.each(data, function(k,v){
			row += '<tr><td>' + v.sec_name + '</td><td>' + v.speed_left + '</td><td>' + v.speed_right + '</td></tr>';
		});
		$('#traffics_table tbody').html(row);
		$('#traffics').perfectScrollbar();
	});
}
function chg_city(id, city){
	$('#weatherReport').weatherfeed([id],{
		woeid: true,
		forecast: true,
		link:	true,
		linktarget: '_self',
		highlow: false
	}, function(){
		var cities = [['基隆市',2306188],['台北市',2306179],['新北市',90717580],['桃園縣',91290407],['新竹市',2306185],['新竹縣',2347334],['苗栗縣',2347338],['台中市',2306181],['彰化縣',20070572],['雲林縣',2347346],['嘉義縣',7153409],['南投縣',2347339],['台南市',2306182],['高雄市',2306180],['屏東縣',2347340],['宜蘭縣',2347336],['花蓮縣',2347335],['台東縣',91290354]];
		var str = '<span>'+city+'</span><i class="fa fa-sort"></i><ul>';
		$.each(cities, function(k,v){
			str += "<li><a onclick=\"chg_city('"+v[1]+"', '"+v[0]+"')\">"+v[0]+"</a></li>";
		});
		str += '</ul>';
		$('.weatherCity').html(str).click(function(){
			$('.weatherCity ul').toggle();
		});
		$('.weatherCity ul').hide();
	});
}
function showLogin(){
	if(login_info.loginid){
		login_info = {};
		$('#login-icon').removeClass('fa-sign-out').addClass('fa-cog');
		alert('已登出');
		$('#layout-elements').addClass('hidden');
		$('.ctl').draggable( 'destroy' )
	}else{
		$('#login-modal').modal('show');
	}
}
function doLogin(){
	var loginid = $('input[name="loginid"]').val();
	var passwd = $('input[name="passwd"]').val();
	if(loginid.length<=0 || passwd.length<=0){
		alert('請輸入帳號及密碼');
		return;
	}
	login_info = {loginid:loginid, passwd:passwd};
	$('#login-icon').removeClass('fa-cog').addClass('fa-sign-out');
	$('#layout-img .ctl').draggable({ containment:"parent" });
	$('#layout-elements .ctl').draggable({revert: true});
	$('#layout-elements').removeClass('hidden');
}
function set_crestron(){
	var ip = $('#crestron-ip').val();
	var name = $('#crestron-name').val();
	get_data('_includes/devices.php?cmd=add_crestron', {ip:ip, name:name}, function(res){
		$('#add-crestron-modal').modal('hide');
		refresh_devices(true);
	});
}
function parseRSS(url, callback) {
  $.ajax({
    url: document.location.protocol + '//ajax.googleapis.com/ajax/services/feed/load?v=1.0&num=10&callback=?&q=' + encodeURIComponent(url),
    dataType: 'json',
    success: function(data) {
      callback(data.responseData.feed);
    }
  });
}
function load_page(url){
	if(history_url.length >= 10) history_url.shift();
	history_url.push(url);
	if(history_url.length > 1) $("#go_prev").show(); 
	location.href=url;
}
function reload_page(){
	if(cururl.length > 0 ) load_page(cururl);
}
function prev_page(){
	if(history_url.length > 1){
		var cur_url = history_url.pop();
		var prev_url = history_url.pop();
		load_page(prev_url);
	}
}
function set_var(selector, value){
	$(selector).val(value);
	return true;
}
function register_form(frm, func_callback, func_before){
	$(frm).ajaxForm({
		beforeSubmit: func_before,
		success: function(ret){
			proc_ret(ret, func_callback);
		}
	});
}
function submit_form(frm, func_callback, func_before){
	$(frm).ajaxSubmit({
		beforeSubmit: func_before,
		success: function(ret){
			proc_ret(ret, func_callback);
		}
	});
}
function get_data(url, send_data, proc_handler){
	$.ajax({
		url:url,
		type:'post',
		data:send_data,
		success:function(ret){
			proc_ret(ret, proc_handler);
		}
	});
}
function proc_ret(ret, proc_handler){
	try{
		res = $.parseJSON(ret);
		if(typeof(proc_handler) == 'function') proc_handler(res);
	}catch(e){
		console.log(e);
		msgbox_show('系統錯誤，可能是連線中斷，請稍候再試！');
		console.log(ret);
	}
}
function msgbox_show(msg, alertDismissed){
	alert(msg);
	if(alertDismissed)alertDismissed;
}