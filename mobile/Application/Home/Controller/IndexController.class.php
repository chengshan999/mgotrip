<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    /*默认首页*/
    public function index(){
        $this->display();
    }

    /*首页起始站输入栏*/
    public function origin(){
        $this->display();
    }

    /*首页目的地输入栏*/
    public function destination(){
        $this->display();
    }

}