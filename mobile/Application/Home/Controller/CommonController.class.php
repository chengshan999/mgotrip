<?php
namespace Home\Controller;
use Think\Controller;

class CommonController extends Controller {

    protected $seodatas = array();

    protected function _initialize() {
        $this->_user_info = session('USER_INFO');

        $SettingApi  = new \Common\Api\SettingApi();
        $this->_CONFIG = $SettingApi->fetchAll();

        $this->assign('_user_info',$this->_user_info);//获取用户登录信息
		$this->assign('_config',   $this->_CONFIG);//获取网站配置信息
    }

    public function display($templateFile = "", $charset = "", $contentType = "", $content = "", $prefix = "")
    {
        $this->seo();
        parent::display($templateFile, $charset, $contentType, $content = "", $prefix = "");
    }

    private function seo()
    {
        $SeoApi  = new \Common\Api\SeoApi();
        $seo = $SeoApi->fetchAll();

        $this->seodatas["sitename"] = $this->_CONFIG["site"]["sitename"];
        $this->seodatas["sitekeywords"] = $this->_CONFIG["site"]["sitekeywords"];
        $this->seodatas["sitedescription"] = $this->_CONFIG["site"]["sitedescription"];
        $key = strtolower(CONTROLLER_NAME . "_" . ACTION_NAME);

        if (isset($seo[$key])) {
            $this->assign("seo_title", tmplToStr($seo[$key]["seo_title"], $this->seodatas));
            $this->assign("seo_keywords", tmplToStr($seo[$key]["seo_keywords"], $this->seodatas));
            $this->assign("seo_description",tmplToStr($seo[$key]["seo_desc"], $this->seodatas));
        }else {
            $this->assign("seo_title", $this->_CONFIG["site"]["sitename"]);
            $this->assign("seo_keywords", $this->_CONFIG["site"]["sitekeywords"]);
            $this->assign("seo_description", $this->_CONFIG["site"]["sitedescription"]);
        }
    }


    //对象转为数组
    protected function objectToArray($e){
    	$e=(array)$e;
    	foreach($e as $k=>$v){
    		if( gettype($v)=='resource' ) return;
    		if( gettype($v)=='object' || gettype($v)=='array' )
    			$e[$k]=(array)objectToArray($v);
    	}
    	return $e;
    }
    
    //数组转对象
	protected function arrayToObject($e){
	    if( gettype($e)!='array' ) return;
	    foreach($e as $k=>$v){
	        if( gettype($v)=='array' || getType($v)=='object' )
	            $e[$k]=(object)arrayToObject($v);
	    }
	    return (object)$e;
	}

	//生成验证码
	public function verify(){
		$verify = new \Think\Verify();
		$verify->fontttf = 'verify.ttf';
		$verify->codeSet = '0123456789';
		$verify->useCurve = false;
		$verify->entry(1);
	}
	
	//判断验证码是否正确
	public function checkVerify(){
	    $verify=I('post.verify','trim');
		if(!check_verify($verify)){
    		return false;
    	}
    	return true;
	}
	  
}