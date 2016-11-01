<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<title>预付365商城-触屏版</title>
<meta name="keywords" content="<?php echo ($seo_keywords); ?>" />
<meta name="description" content="<?php echo ($seo_description); ?>" />
<meta charset="utf-8">
<meta name="viewport" content="width=device-width ,initial-scale=1, maximum-scale=1, user-scalable=yes, minimal-ui">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="format-detection" content="telephone=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-touch-fullscreen" content="yes">
<meta name="full-screen" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<link rel="stylesheet" href="/mgotrip/mobile/<?php echo (THEME_PATH); ?>static/css2016/public.css?v=201605011">
<link rel="stylesheet" href="/mgotrip/mobile/<?php echo (THEME_PATH); ?>static/css2016/index.css?v=201605011">
<link rel="stylesheet" href="/mgotrip/mobile/<?php echo (THEME_PATH); ?>static/css2016/swiper-3.3.1.min.css?v=201605011">
<script type="text/javascript" src="/mgotrip/mobile/<?php echo (THEME_PATH); ?>static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/mgotrip/mobile/<?php echo (THEME_PATH); ?>static/js/swiper-3.3.1.min.js"></script>
<script type="text/javascript" src="/mgotrip/mobile/<?php echo (THEME_PATH); ?>static/js/common.js?v=20160524"></script>
<script type="text/javascript" src="/mgotrip/mobile/<?php echo (THEME_PATH); ?>static/js/layer/layer.js"></script>
<style type="text/css">
    * {tap-highlight-color: rgba(0,0,0,0); -webkit-tap-highlight-color: rgba(0,0,0,0); -ms-tap-highlight-color: rgba(0,0,0,0); box-sizing: border-box; padding: 0; margin: 0;}
</style>
<body>
<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "//hm.baidu.com/hm.js?964970675e477c9c8fa1afcf7a631ecf";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
<script type="text/javascript" src="/mgotrip/mobile/<?php echo (THEME_PATH); ?>static/js/jquery.SuperSlide.2.1.js"></script>
<script type="text/javascript" src="/mgotrip/mobile/<?php echo (THEME_PATH); ?>static/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/mgotrip/mobile/<?php echo (THEME_PATH); ?>static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript">
$(function(){
if (!$.cookie('show_env_2')) {        
        $('.div_shadebox').show(); 
        $('body').addClass('go_to');                     
        setcookie('show_env_2',1);
        $('.a_close').click(function() {            
            $('.div_shadebox').hide();
            $('body').removeClass('go_to');  
            //$.cookie('show_env',{expires:7});   
            setcookie('show_env_2',1);
        });
    }
    function setcookie(name,value){  
        var Days = 1;  
        var exp  = new Date();  
        exp.setTime(exp.getTime() + Days*24*60*60*1000);  
        document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();  
    }
    })
</script>
<!--增加遮罩-->


<!--顶部end-->


<section id="wrap_body">
    <section id="imgShow">
        <div class="swiper-container" id="swiper">
            <div class="swiper-wrapper"></div>
            <div class="swiper-pagination"></div>
        </div>
    </section>
    <!--轮播播放end-->
    <section id="part1" class="items">
        <ul class="clearfix">
        <li class="li_9">
                <a href="">
                    <img class="icon m_img_0" src="/mgotrip/mobile/<?php echo (THEME_PATH); ?>static/img/icon7.png" />
                    <!--  <i class="cion shop"></i>
                -->
                <div>入驻商家</div>
            </a>
        </li>
        <li class="li_9">
            <a href="">
                <img class="icon m_img_1" src="/mgotrip/mobile/<?php echo (THEME_PATH); ?>static/img/icon3.png" />
                <!-- <i class="cion meishi"></i>
            -->
            <div>休闲美食</div>
        </a>
		</li>
		<li class="li_9">
			<a href="">
				<img class="icon m_img_2" src="/mgotrip/mobile/<?php echo (THEME_PATH); ?>static/img/icon4.png" />
				<!--  <i class="cion sheying"></i>
			-->
			<div>致摄影</div>
		</a>
		</li>
		<li class="li_9">
			<a href="">
				<img class="icon m_img_3" src="/mgotrip/mobile/<?php echo (THEME_PATH); ?>static/img/icon5.png" />
				<!-- <i class="cion bed"></i>
			-->
			<div>床上用品</div>
		</a>
		</li>
	</ul>
</section>







<footer>
<nav class="clearfix">
<a href="/help/index.html">帮助中心</a>
<a class="appDown footAppDown" href="http://www.yufu365.com/appMall/app.html">App下载</a>
<a href="/help/about2.html">关于我们</a>
<a href="tel:400-1720-365">联系我们</a>
</nav>
<h3>Copyright © 2016 沪ICP备15051403号</h3>
<h3>上海邑智信息科技有限公司版权所有</h3>
<div class="yufu_footer_copyright_footer"></div>
</footer>
<!-- 底部 end -->

<div class="fixedtop">
<a href="javascript:void(0)" class="fix_go_top"></a>
</div>
</section>
<div class="cart1">
    <ul class="h5_footer">
        <li>
            <a href="<?php echo U('Index/index');?>">
                <span id="footmenu1" class="i1 focus"></span> <strong>商城</strong>
            </a>
        </li>
        <li>
            <a href="<?php echo U('Category/index');?>">
                <span id="footmenu2" class="i1 share"></span> <strong>分类</strong>
            </a>
        </li>
        <li>
            <a href="<?php echo U('Cart/cart');?>">
                <span class="i1 carts_s"><i class="circles" id="cart_num">0</i></span>
                <strong>购物车</strong>
            </a>
        </li>
        <li>
            <a href="<?php echo U('User/index');?>">
                <span  id="footmenu4"  class="i1 my_user"></span>
                <strong>我的</strong>
            </a>
        </li>
    </ul>
</div>

<script type="text/javascript">

    $(document).ready(function(){
        sumcartcount();
    })
    //计算购物车价格
    function sumcartcount(){
        var n = 0;
        var z_price = 0;
        $.getJSON('<?php echo U('Goodscar/ajax_cart');?>',{"random":Math.random},function(content){
            if(content.isError == false)
            {
                $("#cart_num").html(content.data);
            }else{
                $("#cart_num").html(0);
            }
        });
    }
</script>
<script>
$(function(){
    var p_length = $('.swiper-wrapper img').length;    
        if(p_length < 2){return false;};
        var mySwiper = new Swiper('.swiper-container',{
        autoplay : 5000,
        loop: true,
        paginationClickable :true,
        pagination: '.swiper-pagination',
        autoplayDisableOnInteraction: false,
        simulateTouch: true
    });
     })
</script>
<script type="text/javascript">
jQuery(".txtScroll-top").slide({mainCell:"ul",autoPage:true,effect:"topLoop",autoPlay:true,scroll:2,vis:2,delayTime:400});
    //检查关键字查询
    function check_keyword(){
        var pkeyword = $("#pkeyword").val();
        if(pkeyword == ""){
            layer.msg("请输入需查询的关键字");
            return false;
        }else{
            return true;
        }
    }
</script>
</body>