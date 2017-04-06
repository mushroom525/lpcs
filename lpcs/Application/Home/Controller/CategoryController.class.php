<?php
/**
 * Created by PhpStorm.
 * User: heeyhome
 * Date: 2017/3/31
 * Time: 10:21
 */

namespace Home\Controller;
use Think\Controller;

class CategoryController extends Controller
{
    public function catelist(){
        $callback=$_GET['callback'];
        $cate=D('category');
        $data['parent_id']=0;
        $data['if_show']=1;
        $catelist=$cate->where('parent_id=0')->field('cate_id,cate_name')->order('sort_order',asc)->select();
//        $today=array("cate_id"=>"0","cate_name"=>"今日推荐");
//        array_unshift($catelist,$today);
        if ($catelist) {
            $arr = array(
                "code" => "000",
                "msg" => "",
                "data" => $catelist
            );
            echo $callback . "(" . HHJson($arr) . ")";
        } else {
            $arr = array(
                "code" => "111",
                "msg" => "信息不存在",
                "data" => ""
            );
            echo $callback . "(" . HHJson($arr) . ")";
        }
    }
}