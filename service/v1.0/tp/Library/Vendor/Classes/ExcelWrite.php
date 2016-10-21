<?php 
require_once 'PHPExcel.php';
require_once 'PHPExcel/Reader/Excel2007.php';
require_once 'PHPExcel/Reader/Excel5.php';
include_once 'PHPExcel/IOFactory.php';
 
$fileName = "test_excel";
$headArr = array("第一列","第二列","第三列");
$data = array(array(1,2),array(1,3),array(5,7));
getExcel($fileName,$headArr,$data);
 
 
function getExcel($fileName,$headArr,$data){
    if(empty($data) || !is_array($data)){
        die("data must be a array");
    }
    if(empty($fileName)){
        exit;
    }
    $date = date("YmdHis",time());
    $fileName .= "{$date}.xlsx";
 
    //创建新的PHPExcel对象
    $objPHPExcel = new PHPExcel();
    $objProps = $objPHPExcel->getProperties();
     
    //设置表头
    $key = ord("A");
    foreach($headArr as $v){
        $colum = chr($key);
        $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
        $key += 1;
    }
     
    $column = 2;
    $objActSheet = $objPHPExcel->getActiveSheet();
    foreach($data as $key => $rows){ //行写入
        $span = ord("A");
        foreach($rows as $keyName=>$value){// 列写入
            $j = chr($span);
            $objActSheet->setCellValue($j.$column, $value);
            $span++;
        }
        $column++;
    }
 
    $fileName = iconv("utf-8", "gb2312", $fileName);
    //重命名表
    $objPHPExcel->getActiveSheet()->setTitle('Simple');
    //设置活动单指数到第一个表,所以Excel打开这是第一个表
    $objPHPExcel->setActiveSheetIndex(0);
	header("Content-Type: application/force-download"); 
        header("Content-Type: application/octet-stream"); 
        header("Content-Type: application/download"); 
        header('Content-Disposition:inline;filename=\"$fileName\"'); 
        header("Content-Transfer-Encoding: binary"); 
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
        header("Pragma: no-cache"); 
        $obj_Writer->save('php://output'); 
    //将输出重定向到一个客户端web浏览器(Excel2007)
	/*header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header("Content-Disposition: attachment; filename=\"$fileName\"");
	header('Cache-Control: max-age=0');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
	if(!empty($_GET['excel'])){
		$objWriter->save('php://output'); //文件通过浏览器下载
    }else{
    	$objWriter->save($fileName); //脚本方式运行，保存在当前目录
    }*/
  	exit;
}
 ?>