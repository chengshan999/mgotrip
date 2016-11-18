$(function() {
	$('.mgo_map_index').height($(window).height());
	$('.mgo_map_index').width($(window).width());
	//返回上一级
	/*$('.arrow_r').click(function(){
		window.history.back();
	})*/


  });
//生成加载层
  function createLoadingLayer(){
    layer.open({
      type: 2,
      content: '请稍候',
      shadeClose:false
    });
  }
