//$(window).load(function () {
//});

//依傳入的自訂編號(fileUploadNum)，決定要執行哪個上傳控件，的上傳或刪除
jQuery.fn.loadUploadContent = function (fileUploadNum) {
    //调用wrap方法，为id为demo的div外层添加form元素，指定enctype为文件类型，action指定为asp.net文件
    $("#divUploadArea" + fileUploadNum).wrap("<form id='UploadForm" + fileUploadNum + "' action='/index.php/Home/Return/uploadReturnImg' method='post' enctype='multipart/form-data'></form>");

    //var showimg = $('#showimg');          //显示图片的div
    var showimg = $('#divUploadArea' + fileUploadNum);

    $("#btn_file" + fileUploadNum).change(function () {  //当上传文件改变时，触发事件 (不必按鈕，使用者選完圖片，就直接執行上傳的動作)
	   $("#UploadForm" + fileUploadNum).ajaxSubmit({         //调用jquery.form插件的ajaxSubmit异步地提交表单
            dataType: 'json',             //返回数据类型为json
            beforeSend: function () {     //发送数据之前，执下的代码
               // showimg.empty();          //清空图片预览区
               // progress.show();          //显示进度
               // var percentVal = '0%';    //显示进度百分比
              //  bar.width(percentVal);    //设置进度的宽度，增涨进度
               // percent.html(percentVal); //设置进度值
              //  btn.html("上传中...");    //指定显示中
            },
            //更新进度条事件处理代码
          /*  uploadProgress: function (event, position, total, percentComplete) {
                var percentVal = percentComplete + '%';
                bar.width(percentVal)     //更新进度条的宽度
                percent.html(percentVal);
            },*/
            success: function (data) {    //图片上传成功时
                //获取服务器端返回的文件数据
                //alert('success~');
				console.log(data.info);
				if (data.status == "1") { //回傳 1 代表上傳成功，就顯示:預覽圖、刪除超連結
                   
                    var img = "http://pic.test.yufu365.com/mall/return/" + data.info.savename;        //得到文件路径(用來預覽已上傳的圖片, 原圖)
                    
					$("#btn_file" + fileUploadNum).val("").hide();
					//var btnfile = $("#btn_file" + fileUploadNum);
					//btnfile.after(btnfile.clone().val("").hide()); 
					//btnfile.remove(); 

					$("<img src='" + img + "'><i class='close_btn DelImg' id='spanDelImg" + fileUploadNum + "' relNewName='" + data.info.savename+ "'></i>").appendTo('#divUploadArea' + fileUploadNum);
                   
					$("#divUploadArea" + fileUploadNum).removeClass('noImgIcon');
                }
                else if (data.status == "0") {
				   alert(data.info);
                }
               
            },
            error: function (xhr, errorMsg, errorThrown) {  //图片上传失败时 (後端.NET錯誤:回傳型別錯誤會進入此error區塊)
                alert('上传失败，上传图片尺寸不可大于5M');
            }
        });
    });

    $("#spanDelImg" + fileUploadNum).live('touchstart', function () {  //为删除按钮关联事件处理代码，这里用了live
        var picNewName = $(this).attr("relNewName");    //得到图片路径 (截圖後的新圖)
        //向服务器发送删除请求
        $.post("/index.php/Home/Return/DelReturnImg", {name: picNewName }, function (msg) {
            if (msg == "1") {
               // showimg.html(" <input type='file' srcoldid='' id='btn_file"+fileUploadNum+"' name='btn_file"+fileUploadNum+"' class='btnImgFile'> ");
		   		$('#divUploadArea' + fileUploadNum + ' i').remove();
				$('#divUploadArea' + fileUploadNum + ' img').remove();
				$("#btn_file" + fileUploadNum).show();
				showimg.addClass('noImgIcon');
            }else{
				alert('删除失败，请稍后重试');	
			}
           
        });
    });

} //end of "jQuery.fn.loadUploadContent = function (fileUploadNum)"