$(function() {	
	$(window).scroll(function() {
		var scrollTop = $(window).scrollTop();		
		var imgShow = $('#imgShow').outerHeight();
		var search_bar = $('.op_div').outerHeight();				
		if (scrollTop > imgShow) {
			$('.fix_go_top').addClass('fixed');
		} else {
			$('.fix_go_top').removeClass('fixed');
		}		
	});
	$('.fix_go_top').click(function() {
		$('html,body').animate({
			scrollTop: '0px'
		}, 800);
	});		
})