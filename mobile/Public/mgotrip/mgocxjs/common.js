$(function() {
	$('.mgo_map_index').height($(window).height());
	$('.mgo_map_index').width($(window).width());
	//返回上一级
	$('.arrow_r').click(function(){
		window.history.back();
	})
})

