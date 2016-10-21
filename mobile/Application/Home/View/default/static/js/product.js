$(function() {
	var win_h = $(window).height(),		
		touch_h1 = win_h * 1-88;

	//点击购买弹出
	$('#areas').click(function() {
		$('.yf-choose1').addClass('page-slideLeft');
		$('.yf-choose1').show();
		$('.region-wrapper').height(touch_h1);

	});

	$('#btn_exit1').click(function() {
		$('.yf-choose').hide();
		$('.yf-choose').removeClass('page-slideLeft');		
	});
	$('#btn_exit2').click(function() {
		$('.yf-choose1').hide();
		$('.yf-choose1').removeClass('page-slideLeft');		
	});
	function changeCount(obj, count) {
		var _this = $(obj);
		var input = _this.siblings('.goodsCount');
		if (input.val() == '') {
			input.val(1);
		} else {
			var goodsCount = parseInt(input.val()) + count;
			if (goodsCount > 0) {
				input.val(goodsCount);
			}
		}
	}
	$(".decCount").each(function(index, element) {
		$(this).click(function() {			
			changeCount(this, -1);
		});
	});
	$(".addCount").each(function(index, element) {
		$(this).click(function() {
			changeCount(this, 1);
		});
	});
    $('.head-address-ul li').click(function() {
        $(".head-address-ul li").removeClass("head-address-li");
        $(this).addClass("head-address-li")
        var n = $(this).attr("rel");
        $(".address-content ul").hide();
        $("#address_ul"+n).show();

    });
    //多参数
    $('.base_txt').click(function(){
    	var childrens = $('.nature_container');
    	if(childrens.is(':visible')){
    		childrens.slideUp();
    		$('.icon-arrow').addClass('arrow-fold');    		
    	}else{
    		childrens.slideDown();
    		$('.icon-arrow').removeClass('arrow-fold');
    	}
    });
    $('.pro_color .a_item').click(function(){
    	$(this).addClass('selected').siblings().removeClass('selected');
    })
});