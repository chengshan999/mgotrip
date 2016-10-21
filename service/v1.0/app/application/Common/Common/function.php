<?php

/**
 * 检测验证码
 */
function check_verify($code, $id = 1){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}


/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 */
function data_auth_sign($data) {
    //数据类型检测
    if(!is_array($data)){
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}



/**
 * 过滤不安全的HTML代码
 */
function SecurityEditorHtml($str) {
    $farr = array(
        "/\s+/", //过滤多余的空白
        "/<(\/?)(script|i?frame|style|html|body|title|link|meta|\?|\%)([^>]*?)>/isU",
        "/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU"
    );
    $tarr = array(
        " ",
        "＜\\1\\2\\3＞",
        "\\1\\2",
    );
    $str = preg_replace($farr, $tarr, $str);
    return $str;
}



function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    $fix='';
    if(strlen($slice) < strlen($str)){
        $fix='...';
    }
    return $suffix ? $slice.$fix : $slice;
}

function objectToArray($e) {
    $e = (array) $e;
    foreach ($e as $k => $v) {
        if (gettype($v) == 'resource')
            return;
        if (gettype($v) == 'object' || gettype($v) == 'array')
            $e[$k] = (array) objectToArray($v);
    }
    return $e;
}


function getDirName($dir) {
    $dh = opendir($dir);
    $return = array();
    while ($file = readdir($dh)) {
        if ($file != "." && $file != "..") {
            $fullpath = $dir . "/" . $file;
            if (is_dir($fullpath)) {
                $return[$file] = $file;
            }
        }
    }
    closedir($dh);
    return $return;
}

function tmplToStr($str, $datas) {
    preg_match_all('/{(.*?)}/', $str, $arr);

    foreach ($arr[1] as $k => $val) {
        $v = isset($datas[$val]) ? $datas[$val] : '';
        $str = str_replace($arr[0][$k], $v, $str);
    }
    return $str;
}

function delFileByDir($dir) {
    $dh = opendir($dir);
    while ($file = readdir($dh)) {
        if ($file != "." && $file != "..") {
            $fullpath = $dir . "/" . $file;

            if(is_dir($fullpath)) {
                delFileByDir($fullpath);
            }else{
                unlink($fullpath);
            }
        }
    }
    closedir($dh);
}



 function check_mobile($str='')
{
    return (bool)preg_match("!^1[3|4|5|7|8][0-9]\d{8}$!",$str);
}


function sortdata($catArray, $id = 0 , $prefix = '')
{
     $formatCat = array();
     $floor     = 0;

    foreach($catArray as $key => $val)
    {
        if($val['parent_id'] == $id)
        {
            $str         = nstr($prefix,$floor);
            $val['name'] = $str.$val['name'];

            $val['floor'] = $floor;
            $formatCat[]  = $val;

            unset($catArray[$key]);

            $floor++;
            sortdata($catArray, $val['id'] ,$prefix);
            $floor--;
        }
    }
    return $formatCat;
}



//处理商品列表显示缩进
 function nstr($str,$num=0)
{
    $return = '';
    for($i=0;$i<$num;$i++)
    {
        $return .= $str;
    }
    return $return;
}


function encode_json($param)
{
    if(version_compare(phpversion(),'5.4.0') >= 0)
    {
        return json_encode($param,JSON_UNESCAPED_UNICODE);
    }
    $result = '';
    $result = json_encode($param);
    //对于中文的转换
    return preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
}

 function decode_json($string)
{
    if(strpos($string,"\t") !== false)
    {
        $string = str_replace("\t",'',$string);
    }
    return json_decode($string,true);
}


function spu_str($json){
    if($json){
        $spec_array = decode_json($json);
        $str = "";
        foreach ($spec_array as $key1 => $val1) {
            $value = explode(",", $val1['value']);
            $str .= $val1['name'] . "（";
            foreach ($value as $key2 => $val2) {
                $value1 = explode(":", $val2);
                $str .= $value1[1] . ",";
            }
            $str = rtrim($str, ",");
            $str .= "）<br>";
        }
        return $str;
    }else{
        return "---";
    }
}

function spulog_json($json){
    if($json){
        $spec_array = decode_json($json);
        $str = "";
        foreach($spec_array as $key=>$val){
            $str .= $val['name'].":".$val['value']."&nbsp;&nbsp;&nbsp;";
        }
        return $str;
    }else{
        return "---";
    }
}




