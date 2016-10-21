$(function() {
	var win_h = $(window).height(),		
		touch_h = win_h*1-88;		
	//点击购买弹出
	$('#areas').click(function() {
		$('.yf-choose').addClass('page-slideLeft');
		$('.yf-choose').show();
		$('.region-wrapper').height(touch_h);	
		
	});
	$('#btn_exit2').click(function() {
		$('.yf-choose').hide();
		$('.yf-choose').removeClass('page-slideLeft');		
	});
	//弹层		
	$('#consignee').keyup(function() {
		var consignee = $.trim($('#consignee').val());
		if (consignee) {
			$(this).next().show();
		} else {
			$(this).next().hide();
		}
	});
	$('#address').keyup(function() {
		var address = $.trim($('#address').val());
		if (address) {
			$(this).next().show();
		} else {
			$(this).next().hide();
		}
	});
	$('.clear-btn').click(function() {
		var value = $.trim($(this).prev().val());
		if (value) {
			$(this).prev().val('');
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

    $('.head-address-ul li').click(function() {
        $(".head-address-ul li").removeClass("head-address-li");
        $(this).addClass("head-address-li")
        var n = $(this).attr("rel");
        $(".address-content ul").hide();
        $("#address_ul"+n).show();

    });
})