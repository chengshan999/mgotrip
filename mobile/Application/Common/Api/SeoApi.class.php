<?php
namespace Common\Api;

class SeoApi  extends CommonApi {
    protected $token = '';
    protected $cacheTime = 86400;

    //列表
    public function fetchAll(){
        $token = "SeofetchAll";
        $cache = S(array('type' => 'File', 'expire' => $this->cacheTime));
        if (!$data = $cache->get($token)) {
            $data = array();
            $data['act']    = "fetchAll";
            $data['device'] = "H5";
            $data['version']= "1.0";
            $res = $this->postData(MOBILE_SEO,$data);

            $resdata = json_decode($res,true);
            if($resdata['status'] == "succ"){
                $cache->set($token, $resdata['data']);
                return $resdata['data'];
            }else{
                return null;
            }
        }
        return $data;

    }



}