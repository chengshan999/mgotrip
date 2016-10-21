$(function() {
	var msg_errors = {
		'empty_password': '请输入新的登录密码',
		'cir_password': '请输入确认密码',
		'dig_passowrd': '请输入6-20位数字与字母的组合',
		'cdig_passowrd': '两次密码输入不一致',
	}
	$('#next_btn').click(function() {
		var new_password = $.trim($('.new_password').val());
		var old_password = $.trim($('.old_password').val());		
		//验证手机号
		if (!new_password) {
			$('#error').html(msg_errors.empty_password).show();
			return false;
		} else if (new_password.length > 20 || new_password.length < 6 || !num_sibs.test(new_password)) {
			$('#error').html(msg_errors.dig_passowrd).show();
			return false;
		} else {
			$('#error').hide();
		}
		if (!old_password) {
			$('#error').html(msg_errors.cir_password).show();
			return false;
		} else if (new_password !== old_password) {
			$('#error').html(msg_errors.cdig_passowrd).show();
			return false;
		} else {
			$('#error').hide();
		}
	});	
	//按下显示叉叉
	$('.new_password').keyup(function() {
		var new_password = $.trim($('.new_password').val()); //获取手机号
		if (new_password) {
			$(this).parent().next().show();
		}else{
			$(this).parent().next().hide();
		}
	});	
	$('.old_password').keyup(function() {
		var old_password = $.trim($('.old_password').val()); //获取手机号
		if (old_password) {
			$(this).parent().next().show();
		}else{
			$(this).parent().next().hide();
		}
	});	
	//点击清除密码 
	$('.clear-btn').click(function() {
		var val = $.trim($(this).prev().children().val());
		if (val) {
			$(this).prev().children().val('');
			$(this).hide();
		}
	})
})