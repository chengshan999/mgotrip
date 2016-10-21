$(function() {	
	/*tab选项卡切换*/
	$('.main_tabs li').click(function() {
		$(this).addClass('on').siblings().removeClass('on');
		var index = $('.main_tabs li').index(this);
		$('.tab_conbox > div').eq(index).show().siblings().hide();
	});	
})