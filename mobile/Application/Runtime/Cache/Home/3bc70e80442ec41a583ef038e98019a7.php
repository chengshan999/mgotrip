<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
	<title>魔购出行（M-GO）</title>
	<meta name="keywords" />
	<meta name="description"/>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width ,initial-scale=1, maximum-scale=1, user-scalable=yes, minimal-ui">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="format-detection" content="telephone=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-touch-fullscreen" content="yes">
	<meta name="full-screen" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">

	<link rel="stylesheet" href="/mgotrip/mobile/Public/mgotrip/mgocx_css/common.css">
<link rel="stylesheet" href="/mgotrip/mobile/Public/mgotrip/mgocx_css/index.css">
<link rel="stylesheet" href="/mgotrip/mobile/Public/mgotrip/mgocx_css/base.css">
<link rel="stylesheet" href="/mgotrip/mobile/Public/mgotrip/mgocx_css/my.css">
<link rel="stylesheet" href="/mgotrip/mobile/Public/mgotrip/mgocx_css/origin.css">
<link rel="stylesheet" href="/mgotrip/mobile/Public/mgotrip/mgocx_css/need/layer.css">
<script type="text/javascript" src="/mgotrip/mobile/Public/mgotrip/mgocxjs/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/mgotrip/mobile/Public/mgotrip/mgocxjs/common.js"></script>
<script type="text/javascript" src="/mgotrip/mobile/Public/mgotrip/mgocx_css/layer.js"></script>

<body>
	<header class="head_bar">
		<a href="javascript:void(0)" class="arrow_r"></a>
		<span class="c_txt">M-GO</span>
		<a href="javascript:void(0)" class="user_login"></a>
	</header>
	<!--魔购头部-->
	<!--头部搜索-->
	<section class="content">
		<div class="head_search">
			<span class="my_citys">上海</span>
			<div class="input_searchs"> <i class="icon_search"></i>
				<input type="search" class="search_s" placeholder="您现在要去哪儿？" />
			</div>
			<a href="javascript:void(0)" class="cancle_btn">取消</a>
		</div>

		<div class="org_bj">
			<ul class="clearfix panel_ul">
				<li class="current">
					徐汇区漕溪北路2000号 <i></i>
				</li>
				<li>
					龙漕北路199号
					<i></i>
				</li>
				<li>
					番禺路1028号-东门
					<i></i>
				</li>
				<li>
					太平洋百货
					<i></i>
				</li>
				<li>
					汇金百货
					<i></i>
				</li>
				<li>
					瑞金南苑-西门
					<i></i>
				</li>
				<li>
					复兴佳苑
					<i></i>
				</li>
				<li>
					锦秋国际停车场
					<i></i>
				</li>
				<a href="javascript:void(0)" class="clear_record">清空历史记录</a>
			</ul>
		</div>
		<!--登录框 star-->
	<div class="shade3_box">
		<div class="login_box" id="login_box">
			<div class="login_bar">
				<h3>验证手机</h3>
				<a href="javascript:void(0)" class="btn-close-box"></a>
				<!--表单star-->
				<div class="form-box">
					<div class="form-label">
						<input type="tel" name="" placeholder="请输入手机号" class="form-input mobile" />
					</div>
					<div class="form-label">
						<input type="number" name="" placeholder="请输入验证码" class="form-input smaller codes" />
						<!-- <i class="close_i"></i>
					-->
					<span class="r_text">获取验证码</span>
				</div>
				<a href="javascript:void(0)" class="login_btns" disabled="disabled">登录</a>
			</div>
		</div>
	</div>
</div>
	</section>
	<script type="text/javascript">
	$(function(){
		$('.panel_ul li').click(function(){			
			$(this).addClass('current').siblings().removeClass('current');
	});
		$('.user_login').click(function(){
				$('.shade3_box').slideDown();
				$('body').addClass('hidden');
			});
			$('.btn-close-box').click(function(){
				$('.shade3_box').hide();
				$('body,.main_sm').removeClass('hidden');				
				 window.location.reload();
			});		
			$('.form-input').each(function(){
				if($(this).val() ==''){									
					$('.login_btns').removeClass('login_on'); 
					$('.login_btns').attr("disabled","disabled")   
					return false;             
				}  else{
					$('.login_btns').addClass('login_on'); 
					$('.login_btns').removeAttr("disabled");   
				} 
			});
			$(".form-input").keyup("input", function() {        
            var parents =  $(this).parents('.form-box');                             
            parents.find('.form-input').each(function(e){
            	if($(this).val() == ''){
                    $('.login_btns').removeClass('login_on'); 
                    $('.login_btns').attr("disabled","disabled")   
                    return false;             
                }
                	$('.login_btns').addClass('login_on');
                    $('.login_btns').removeAttr("disabled");
                  
            })
        });	

			$('.login_btns').on('click',function(){				
				if($(this).attr('disabled') == 'disabled'){					
					return; 
				}else{
					var mobile = $.trim($('.mobile').val());
					var codes = $.trim($('.codes').val());
					var number = /^(1[3578]\d|14[57])[0-9]{8}$/;
					if(!mobile){
						layer.open({
							content: '请填写手机号码',
							btn: ['确认', '取消']
					});
						return false;
					}else if(!number.test(mobile)){
						// alert('请输入正确的手机号');
						layer.open({
							content: '请输入正确的手机号',
							btn: ['确认', '取消']
						});						
					}
					if(!codes){
						layer.open({
							content: '请填写验证码',
							btn: ['确认', '取消']
					});
						
						return false;
					}
				}				
				
			})
			$('.r_text').click(function(){
				var mobile = $.trim($('.mobile').val());				
				var number = /^(1[3578]\d|14[57])[0-9]{8}$/;
				if(!mobile){
					layer.open({
							content: '请填写手机号码',							
							btn: ['确认', '取消']
					});					
					return false;
				}else if(!number.test(mobile)){
					layer.open({
							content: '请输入正确的手机号',
							btn: ['确认', '取消']
					});					
				}
			})

		})
	</script>
</body>
</html>