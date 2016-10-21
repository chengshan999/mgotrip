$(function(){
	$('.left_fenlei li').click(function(){
		$(this).addClass('cur').siblings().removeClass('cur');
		var index = $('.left_fenlei li').index(this);
		$('.tab_box_cats > div').eq(index).show('').siblings().hide('');
	})
	$('.items_tabs li:nth-child(3n)').addClass('last');
})