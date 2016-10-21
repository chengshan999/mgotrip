$(function() {
	/*爆布流 star*/
	var page = 1;
	var finished = 0;
	var sover = 0;
	//如果屏幕未到整屏自动加载下一页补满  
	var setdefult = setInterval(function() {
		if (sover == 1)
			clearInterval(setdefult);
		else if ($(".pic_list").height() < $(window).height())
			loadmore($(window));
		else
			clearInterval(setdefult);
	}, 2000);
	//加载完  
	function loadover() {
		if (sover == 1) {
			var overtext = "Duang～到底了";
			$(".loadmore").remove();
			if ($(".loadover").length > 0) {
				$(".loadover span").eq(0).html(overtext);
			} else {
				var txt = '<div class="loadover"><span>' + overtext + '</span></div>'
				$(".shop_list").append(txt);
			}
		}
	}
	//加载更多  
	var vid = 0;
	function loadmore(obj) {
		if (finished == 0 && sover == 0) {
			var scrollTop = $(obj).scrollTop();
			var scrollHeight = $('.viewport').height();						
			var windowHeight = $(obj).height();

			if ($(".loadmore").length == 0) {
				var txt = '<div class="loadmore"><span class="loading"></span>加载中..</div>'
				$(".shop_list").append(txt);
			}
			if (scrollTop + windowHeight - scrollHeight <= 50) {
				//此处是滚动条到底部时候触发的事件，在这里写要加载的数据，或者是拉动滚动条的操作  


				//防止未加载完再次执行  
				finished = 1;
				var result = "";
				for (var i = 0; i < 5; i++) {
					vid++;
					result += '<a href="http://www.baidu.com"><li><div class="date"><span class="d">2015年11月11日 15:40</span> <i class="pngBase"></i></div><h4 class="yhq_name">优惠券过期提醒</h4><p class="img_box"><img src="img/img16.jpg"></p><p class="info_list clearfix"><span href="#" class="view_info">查看详情</span><span class="arrow_r"></span></p></li></a>';
				}
				setTimeout(function() {
					//$(".loadmore").remove();  
					$('.pic_list').append(result);
					page += 1;
					finished = 0;
					//最后一页  
					if (page == 10) {
						sover = 1;
						loadover();
					}
				}, 2000);
				/*$.ajax({  
				    type: 'GET',  
				    url: 'json/more.json?t=25&page='+page,  
				    dataType: 'json',  
				    success: function(data){  
				        if(data=="")  
				        {  
				            sover = 1;  
				            loadover();                       
				            if (page == 1) {  
				                $("#no_msg").removeClass("hidden");  
				                $(".loadover").remove();  
				            }  
				        }  
				        else  
				        {  
				            var result = ''  
				            for(var i = 0; i < data.lists.length; i++){  
				                result+='<li>'  
				                            +'<a href="'+data.lists[i].link+'">'+data.lists[i].title+parseInt(page+1)+"-"+i+'</a>'  
				                        +'</li>'  
				            }  
				              
				            // 为了测试，延迟1秒加载  
				            setTimeout(function(){  
				                $(".loadmore").remove();  
				                $('.prolist').append(result);  
				                page+=1;  
				                finished=0;  
				                //最后一页  
				                if(page==10)  
				                {  
				                    sover=1;  
				                    loadover();  
				                }  
				            },1000);  
				        }  
				    },  
				    error: function(xhr, type){  
				        alert('Ajax error!');  
				    }  
				});*/
			}
		}
	}
	//页面滚动执行事件  
	$('.content').scroll(function() {
		loadmore($(this));
	});
})