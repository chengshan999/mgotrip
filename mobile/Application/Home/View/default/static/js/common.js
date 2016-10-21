$(function() {
	$('.op_form').click(function() {
		$('#search_box').show();
		$('#wrap_body,.cart1').hide();
	});
	//取消搜索
	$('.closed').click(function() {
		$('#search_box').hide();
		$('#wrap_body,.cart1').show();
	})
	$('.search_landing_tags a:nth-child(3n)').addClass('last');
	$(window).scroll(function() {
		var scrollTop = $(window).scrollTop();
		var search_bar = $('.op_div').outerHeight();
		var imgShow = $('#imgShow').outerHeight();
		if (scrollTop > search_bar) {
			$('.showfixedtop-half').addClass('fix');
		} else {
			$('.showfixedtop-half').removeClass('fix');
		}
		if (scrollTop > imgShow) {
			$('.fix_go_top').addClass('fixed');
		} else {
			$('.fix_go_top').removeClass('fixed');
		}
	})
	$('.content').scroll(function() {
		var scrollTop = $('.content').scrollTop();
		var imgShow = $('#imgShow').outerHeight();

		if (scrollTop > imgShow) {
			$('.fix_go_top').addClass('fixed');
		} else {
			$('.fix_go_top').removeClass('fixed');
		}

	});
	$('.fix_go_top,.fix_go_top2').click(function() {
		$('html,body,.content').animate({
			scrollTop: '0px'
		}, 200);
	});
	//返回上一页
	$("#btn_exit").bind("click", function() {
		self.location=document.referrer;
	});
	$('.span_click').click(function() {
			var $div_none = $('.show_divs');
			if ($div_none.is(':visible')) {
				$div_none.slideUp();
			} else {
				$div_none.slideDown();
			}
		})
		//点空白处隐藏
	$(document).live('touchstart',function(e) {
		var _con = $('.span_click,.show_divs'); // 设置目标区域
		if (!_con.is(e.target) && _con.has(e.target).length === 0) { // Mark 1
			$('.show_divs').slideUp();
		}
	});
	
})