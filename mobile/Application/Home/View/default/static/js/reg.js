$(function() {
	$('#reg_btn').click(function() {
		var msg_errors = {
			'empty_mobile': '请填写手机号',
			'rele_mobile': '请正确填写您的手机号码',
			'codes': '请填写验证码',
			'Dig_codes': '请输入6位数字验证码',
			'passowrd': '请输入密码',
			'dig_passowrd': '请输入6-20位数字与字母的组合',
			'cpassowrd': '请填写确认密码',
			'cdig_passowrd': '两次密码输入不一致',
		}
		var number = /^(1[3578]\d|14[57])[0-9]{8}$/; //正则
		var num_sibs = /(?!^[0-9]*$)(?!^[a-zA-Z]*$)^([a-zA-Z0-9]{2,})$/; //正则
		var mobile = $.trim($('.mobile').val()); //获取手机号
		var codes = $.trim($('.codes').val()); //获取验证码
		var password = $.trim($('.login_password').val()); //获取6位登录密码 
		var con_password = $.trim($('.con_password').val()); //获取重复密码 
		//验证手机号
		if (!mobile) {
			$('#error').html(msg_errors.empty_mobile).show();
			return false;
		} else if (!number.test(mobile)) {
			$('#error').html(msg_errors.rele_mobile).show();
			return false;
		} else {
			$('#error').hide();
		}
		//验证验证码
		if (!codes) {
			$('#error').html(msg_errors.codes).show();
			return false;
		} else if (codes.length < 6) {
			$('#error').html(msg_errors.Dig_codes).show();
			return false;
		} else {
			$('#error').hide();
		}
		//验证6位数字字母组合密码
		if (!password) {
			$('#error').html(msg_errors.passowrd).show();
			return false;
		} else if (password.length > 20 || password.length < 6 || !num_sibs.test(password)) {
			$('#error').html(msg_errors.dig_passowrd).show();
			return false;
		} else {
			$('#error').hide();
		}
		//验证重复密码 
		if (!con_password) {
			$('#error').html(msg_errors.cpassowrd).show();
			return false;
		} else if (password !== con_password) {
			$('#error').html(msg_errors.cdig_passowrd).show();
			return false;
		} else {
			$('#error').hide();
		}
	});
	//发送验证码
	$('.getCode').click(function() {
		var msg_errors = {
			'empty_mobile': '请填写手机号',
			'rele_mobile': '请正确填写您的手机号码',			
		}
		var number = /^(1[3578]\d|14[57])[0-9]{8}$/; //正则
		var mobile = $.trim($('.mobile').val()); //获取手机号
		var mobile = $.trim($('.mobile').val()); //获取手机号
			if (!mobile) {
				$('#error').html(msg_errors.empty_mobile).show();
				return false;
			} else if (!number.test(mobile)) {
				$('#error').html(msg_errors.rele_mobile).show();
				return false;
			} else {
				$('#error').hide();
			}
		})
		//按下显示叉叉
	$('.mobile').keyup(function() {
		var mobile = $.trim($('.mobile').val()); //获取手机号
		if (mobile) {
			$(this).parent().next().show();
		}else{
			$(this).parent().next().hide();
		}
	});
	$('.login_password').keyup(function() {
		var password = $.trim($('.login_password').val()); //获取6位登录密码 
		if (password) {
			$(this).parent().next().show();
		}else{
			$(this).parent().next().hide();
		}
	})
	$('.con_password').keyup(function() {
		var con_password = $.trim($('.con_password').val()); //获取重复密码 
		if (con_password) {
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
	});
	//点空白处隐藏
	$(document).click(function(e) {
		var _con = $('.clear-btn,input'); // 设置目标区域
		if (!_con.is(e.target) && _con.has(e.target).length === 0) { // Mark 1
			$('.clear-btn').hide();
		}
	});
})