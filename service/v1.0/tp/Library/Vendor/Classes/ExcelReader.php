<?php
require_once 'PHPExcel.php';

class ExcelReader {
	/**
	 * 读取excel
	 * @param unknown_type $excelPath：excel路径
	 * @param unknown_type $allColumn：读取的列数
	 * @param unknown_type $sheet：读取的工作表
	 * @param unknown_type $row：从第几行开始
	 * @param unknown_type $rows：循环总数 - n行，用于去掉表格下面没用的部分
	 * @param unknown_type $type 返回值类型$name_info
	 * @param unknown_type $name_info 下标定义
	 * @param unknown_type $line 所取行数 
	 */

	public function read_excel($excelPath, $type = 0, $name_info=array(), $row = 2, /*$rows,*/ $allColumn, $sheet = 0 ,$line = 0) {
		$excel_arr = array();
		//默认用excel2007读取excel,若格式 不对，则用之前的版本进行读取
		$PHPReader = new PHPExcel_Reader_Excel2007();
		if(!$PHPReader->canRead($excelPath)) {
			$PHPReader = new PHPExcel_Reader_Excel5();
			if(!$PHPReader->canRead($excelPath)) {
				//返回空的数组
				return $excel_arr;		
			}
		}
		//载入excel文件
		$PHPExcel  = new PHPExcel();
		$PHPExcel  = $PHPReader->load($excelPath);
		
		//获取工作表总数
		$sheetCount = $PHPExcel->getSheetCount();
		
		//判断是否超过工作表总数，取最小值
		$sheet = $sheet < $sheetCount ? $sheet : $sheetCount;

		//默认读取excel文件中的第一个工作表
		$currentSheet = $PHPExcel->getSheet($sheet);
		
		if(empty($allColumn)) {
			//取得最大列号，这里输出的是大写的英文字母，ord()函数将字符转为十进制，65代表A
			echo $allColumn = ord($currentSheet->getHighestColumn()) - 65 + 1;
		}
		
		//取得一共多少行
		$allRow = $currentSheet->getHighestRow();
		// 从第n行开始输出，因为excel表中第一行为列名 
		
		// for($currentRow = $row; $currentRow <= $allRow-$rows; $currentRow++) {
		// 	for($currentColumn = 0; $currentColumn <= $allColumn - 1; $currentColumn++) {
		// 		$val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();		
		// 		$excel_arr[$currentRow - 2][$currentColumn] = iconv('utf-8','gb2312', $val);
		// 	}
		// }

		if($line>0){
			$allRow = $line;
		}
		for($currentRow = $row; $currentRow <= $allRow; $currentRow++) {
			for($currentColumn = 0; $currentColumn < $allColumn; $currentColumn++) {
				$val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
				if($val instanceof PHPExcel_RichText){ //富文本转换字符串
					$val = $val->__toString();
				}
				$val = empty($val) ? '' : $val;
				if(is_array($name_info) && !empty($name_info) && count($name_info)==6){//企业员工福利信息导入
					if($name_info[$currentColumn] == 'mobile'){
						$excel_arr[$currentRow - 2][$name_info[$currentColumn]] = str_replace('-','',$val);
					}elseif($name_info[$currentColumn] == 'welfare_amount'){
						$excel_arr[$currentRow - 2][$name_info[$currentColumn]] = $val*100;
					}else{
						$excel_arr[$currentRow - 2][$name_info[$currentColumn]] = $val;
					}
				}
				elseif(is_array($name_info) && !empty($name_info) && $name_info['type']=='goods'){//商品信息信息导入
					$excel_arr[$currentRow - 3][$name_info[$currentColumn]] = $val;
				}
				else{
					$excel_arr[$currentRow - 2][] = $val;
				}
			}
		}
		
		if($type == 1 ){
			//返回二维数组
			return $excel_arr;
		}elseif($type == 0){
			//返回字符串
			$data = '';
			foreach ($excel_arr as $key => $value) {
			    $data .= ',(';
			    for($i=0;$i<count($value);$i++){
			        if($i == 0){
			            $data .= "'".($value[$i])."'";
			        }else{
			            $data .= ",'".($value[$i])."'";
			        }
			    }
			    $data .= ')';
			}
			$data = mb_substr($data,1);
			return $data;	
		}
	}
}
?>