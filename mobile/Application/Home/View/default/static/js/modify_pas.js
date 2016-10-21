// var TabbedContent = {
// 	init: function() {
// 		$(".tab_item").click(function() {
// 			var background = $(this).parent().find(".moving_bg");
// 			$(this).addClass('cur').siblings().removeClass('cur');			
// 			$(background).stop().animate({
// 				left: $(this).position()['left']
// 			}, {
// 				duration: 300
// 			});
// 			TabbedContent.slideContent($(this));
// 		});
// 	},
// 	slideContent: function(obj) {
// 		var margin = $(obj).parents().find(".tab_box").width();
// 		margin = margin * ($(obj).prevAll().size() - 1);
// 		margin = margin * -1;
// 		$(obj).parents().find(".tabslider").stop().animate({
// 			marginLeft: margin + "px"
// 		}, {
// 			duration: 300
// 		});
// 	},
// }
// $(document).ready(function() {
// 	TabbedContent.init();
// });
$(function() {
	var msg_errors = {
		'empty_mobile': '请输入手机号',
		'error_mobile': '手机号码错误，请修改',
		'sms_codes': '请输入短信验证码',
	}
	$('#next_btn').click(function() {
		var mobile = $.trim($('.mobile').val());
		var sms_codes = $.trim($('.sms').val());
		var number = /^(1[3578]\d|14[57])[0-9]{8}$/; //正则
		//验证手机号
		if (!mobile) {
			$('#error').html(msg_errors.empty_mobile).show();
			return false;
		} else if (!number.test(mobile)) {
			$('#error').html(msg_errors.error_mobile).show();
			return false;
		} else {
			$('#error').hide();
		}
		//验证验证码
		if (!sms_codes) {
			$('#error').html(msg_errors.sms_codes).show();
			return false;
		} else {
			$('#error').hide();
		}
	});
	//发送验证码
	$('.getCode').click(function() {
		var msg_errors = {
			'empty_mobile': '请输入手机号',
			'error_mobile': '手机号码错误，请修改',			
		}
		var number = /^(1[3578]\d|14[57])[0-9]{8}$/; //正则
		var mobile = $.trim($('.mobile').val()); //获取手机号		
		if (!mobile) {
			$('#error').html(msg_errors.empty_mobile).show();
			return false;
		} else if (!number.test(mobile)) {
			$('#error').html(msg_errors.error_mobile).show();
			return false;
		} else {
			$('#error').hide();
		}
	});
	//按下显示叉叉
	$('.mobile').keyup(function() {
		var mobile = $.trim($('.mobile').val()); //获取手机号
		if (mobile) {
			$(this).parent().next().show();
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