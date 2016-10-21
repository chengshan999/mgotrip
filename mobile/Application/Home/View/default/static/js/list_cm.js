$(function() {
	/*爆布流 star*/
	var page = 1;
	var finished = 0;
	var sover = 0;
	//如果屏幕未到整屏自动加载下一页补满  
	var setdefult = setInterval(function() {
		if (sover == 1)
			clearInterval(setdefult);
		else if ($(".yfk_items").height() < $(window).height())
			loadmore($(window));
		else
			clearInterval(setdefult);
	}, 500);
	//加载完  
	function loadover() {
		if (sover == 1) {
			var overtext = "Duang～到底了";
			$(".loadmore").remove();
			if ($(".loadover").length > 0) {
				$(".loadover span").eq(0).html(overtext);
			} else {
				var txt = '<div class="loadover"><span>' + overtext + '</span></div>'
				$("body").append(txt);
			}
		}
	}
	//加载更多  
	var vid = 0;
	function loadmore(obj) {
		if (finished == 0 && sover == 0) {
			var scrollTop = $(obj).scrollTop();
			var scrollHeight = $(document).height();
			var windowHeight = $(obj).height();

			if ($(".loadmore").length == 0) {
				var txt = '<div class="loadmore"><span class="loading"></span>加载中..</div>'
				$("body").append(txt);
			}
			if (scrollTop + windowHeight - scrollHeight <= 50) {
				//此处是滚动条到底部时候触发的事件，在这里写要加载的数据，或者是拉动滚动条的操作  


				//防止未加载完再次执行  
				finished = 1;
				var result = "";
				for (var i = 0; i < 5; i++) {
					vid++;
					result += '<a href="http://www.yufu365.com"><li class="clearfix"><p class="fl_l img_yfk"><img src="img/1435630808fKfd.jpg" /></p><p class="items_p"><p class="items_p">锦江E卡通</span><span class="item_m">锦江E卡通是锦江国际商务有限公司打造的全新电子储值卡</span><span class="items_btn">专享</span></li></a>';
				}
				setTimeout(function() {
					//$(".loadmore").remove();  
					$('.yfk_items').append(result);
					page += 1;
					finished = 0;
					//最后一页  
					if (page == 10) {
						sover = 1;
						loadover();
					}
				}, 1000);
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
	$(window).scroll(function() {
		loadmore($(this));
	});
})