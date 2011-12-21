function reduce_trade_amount(id)
{
    var input = $('trade_amount_'+ id) ;
    var block_input = $('block_trade_amount_'+ id) ;
    var new_amount = parseInt(input.value)-1;
    if(new_amount<=0)
    {
        new_amount = 1;
    }
    input.value = new_amount;
    if(block_input)
    {
        block_input.value=input.value
    }
}
function increase_trade_amount(id)
{
    var input = $('trade_amount_'+ id) ;
    var new_amount = parseInt(input.value)+1;
    var block_input = $('block_trade_amount_'+ id) ;
    if(new_amount>99)
    {
        new_amount = 99;
    }
    input.value = new_amount;
        if(block_input)
    {
        block_input.value=input.value
    }
}
function set_trade_amount(id)
{
    var input = $('trade_amount_'+ id) ;
    var block_input = $('block_trade_amount_'+ id) ;
    var new_amount = parseInt(input.value);
    if(new_amount>99)
    {
        new_amount = 99;
    }else if(new_amount<=0)
    {
        new_amount = 1;
    }
    input.value = new_amount;
        if(block_input)
    {
        block_input.value=input.value
    }
}
function addTradeCart(tu_id, tp_id)
{
    var amount = parseInt($('trade_amount_' + tp_id).value);
    var url = 'plugin.php?id=etuan:cart&op=add&tuan_id='+tu_id+"&product_id="+tp_id+"&quantity="+amount+"&inajax=1";
    ajaxget(url, 'cart_dummy','cart_dummy', '', '', function(){
        showDialog('商品已成功加入购物车！', 'notice', '购物车', '', 1, '', '<a href="plugin.php?id=etuan:cart">点击这里查看购物车</a><button onclick="window.location=\'plugin.php?id=etuan:cart&op=checkout\';" class="pn pnc" title="去结账"><strong>去结账<strong></button>', '继续购物');
    });

    return false;
}

var etuanDialogST = null;
function etuanDialog(t, mode, msg, func, cover, funccancel, leftmsg, confirmtxt, canceltxt, closetime, locationtime) {
	clearTimeout(etuanDialogST);
	cover = isUndefined(cover) ? (mode == 'info' ? 0 : 1) : cover;
	leftmsg = isUndefined(leftmsg) ? '' : leftmsg;
	mode = in_array(mode, ['confirm', 'notice', 'info', 'right']) ? mode : 'alert';
	var menuid = 'fwin_dialog';
	var menuObj = $(menuid);
	confirmtxtdefault = '确定';
	closetime = isUndefined(closetime) ? '' : closetime;
	closefunc = function () {
		if(typeof func == 'function') func();
		else eval(func);
		hideMenu(menuid, 'dialog');
	};
	if(closetime) {
		leftmsg = closetime + ' 秒后窗口关闭';
		etuanDialogST = setTimeout(closefunc, closetime * 1000);
	}
	locationtime = isUndefined(locationtime) ? '' : locationtime;
	if(locationtime) {
		leftmsg = locationtime + ' 秒后页面跳转';
		etuanDialogST = setTimeout(closefunc, locationtime * 1000);
		confirmtxtdefault = '立即跳转';
	}
	confirmtxt = confirmtxt ? confirmtxt : confirmtxtdefault;
	canceltxt = canceltxt ? canceltxt : '取消';

	if(menuObj) hideMenu('fwin_dialog', 'dialog');
	menuObj = document.createElement('div');
	menuObj.style.display = 'none';
	menuObj.className = 'fwinmask';
	menuObj.id = menuid;
	$('append_parent').appendChild(menuObj);
	var hidedom = '';
	if(!BROWSER.ie) {
		hidedom = '<style type="text/css">object{visibility:hidden;}</style>';
	}
	var s = hidedom + '<table cellpadding="0" cellspacing="0" class="fwin"><tr><td class="t_l"></td><td class="t_c"></td><td class="t_r"></td></tr><tr><td class="m_l">&nbsp;&nbsp;</td><td class="m_c"><h3 class="flb"><em>';
	s += t ? t : '提示信息';
	s += '</em><span><a href="javascript:;" id="fwin_dialog_close" class="flbc" onclick="hideMenu(\'' + menuid + '\', \'dialog\')" title="关闭">关闭</a></span></h3>';
	if(mode == 'info') {
		s += msg ? msg : '';
	} else {
		s += '<div class="c altw"><div class="' + (mode == 'alert' ? 'alert_error' : (mode == 'right' ? 'alert_right' : 'alert_info')) + '"><p>' + msg + '</p></div></div>';
		s += '<p class="o pns">' + (leftmsg ? '<span class="z xg1">' + leftmsg + '</span>' : '') + '<button id="fwin_dialog_submit" value="true" class="pn pnc"><strong>'+confirmtxt+'</strong></button>';
		s += mode == 'confirm' ? '<button id="fwin_dialog_cancel" value="true" class="pn" onclick="hideMenu(\'' + menuid + '\', \'dialog\')"><strong>'+canceltxt+'</strong></button>' : '';
		s += '</p>';
	}
	s += '</td><td class="m_r"></td></tr><tr><td class="b_l"></td><td class="b_c"></td><td class="b_r"></td></tr></table>';
	menuObj.innerHTML = s;
	if($('fwin_dialog_submit')) $('fwin_dialog_submit').onclick = function() {
		if(typeof func == 'function') func();
		else eval(func);
		hideMenu(menuid, 'dialog');
	};
	if($('fwin_dialog_cancel')) {
		$('fwin_dialog_cancel').onclick = function() {
			if(typeof funccancel == 'function') funccancel();
			else eval(funccancel);
			hideMenu(menuid, 'dialog');
		};
		$('fwin_dialog_close').onclick = $('fwin_dialog_cancel').onclick;
	}
	showMenu({'mtype':'dialog','menuid':menuid,'duration':3,'pos':'00','zindex':JSMENU['zIndex']['dialog'],'cache':0,'cover':cover});
	try {
		if($('fwin_dialog_submit')) $('fwin_dialog_submit').focus();
	} catch(e) {}
}