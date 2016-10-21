<?php
namespace Common\Api;

class CommonApi  {
    protected $token = '';
    protected $cacheTime = 86400;



    public function getReturn($url, $data)
    {
        $post_data = json_encode($data);
        return $this->postData($url, $post_data);
    }

    public function postData($url, $post_data)
    {
        if (is_array($post_data) || is_object($post_data)) {
            $post_data = http_build_query($post_data);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $ret = curl_exec($ch);
        curl_close($ch);

        return $ret;
    }

    protected function objectToArray($e){
        $e=(array)$e;
        foreach($e as $k=>$v){
            if( gettype($v)=='resource' ) return;
            if( gettype($v)=='object' || gettype($v)=='array' )
                $e[$k]=(array)objectToArray($v);
        }
        return $e;
    }


}