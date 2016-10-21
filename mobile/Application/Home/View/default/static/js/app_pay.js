$(function() {
	//$('#next_btn').click(function() {
	//	var passowrd = $('.pwd-input').val();
	//	if (!passowrd) {
	//		$('#error').html('请输入6位支付密码').show();
	//	} else if (passowrd.length < 6) {
	//		$('#error').html('支付密码格式错误').show();
	//	} else {
	//		$('#error').hide();
	//	}
	//})
	var $input = $(".fake-box input"); //假框输入
	$("#pwd-input").on("input", function() {
		var pwd = $(this).val().trim();
		for (var i = 0, len = pwd.length; i < len; i++) {
			$input.eq("" + i + "").val(pwd[i]);
			// $input.eq("" + i + "").addClass('on').siblings().removeClass('on');		
		}
		$input.each(function() {
			var index = $(this).index();
			if (index >= len) {
				$(this).val("");
			}
		});
		if (len == 6) {
			//执行其他操作
			$('.go_btn_n').addClass('current');
		}
	});
})