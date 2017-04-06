<?php
/**
 * Created by PhpStorm.
 * User: heeyhome
 * Date: 2017/3/29
 * Time: 13:31
 */

namespace Admin\Controller;
class GoodsController extends CommonController
{
    public function index()
    {
        $goods = D('goods');
        $cate = D('category');
        $list=$goods->select();
        foreach ($list as $key=>$val){
            $cate_ids=explode(',',$val['cate_id']);
            $cate_name='';
            foreach($cate_ids as $k=>$v){
                $name=$cate->where('cate_id='.$cate_ids[$k])->getField('cate_name');
                if($name){
                    $cate_name=$cate_name.$name.' / ';
                }
            }
            $list[$key]['cate_name']=substr($cate_name, 0,-2);
            if($val['tag']==0){
                $list[$key]['tag']='';
            }else if($val['tag']==1) {
                $list[$key]['tag'] = '今日推荐';
            }else{
                $list[$key]['tag'] = '特惠供应';
            }
        }
        //dump($list);die;
        $this->assign('goods',$list);
        $this->display();
    }

    public function add()
    {
        if (!IS_POST) {
            $cate = D('category');
            $catelist = $cate->where('parent_id=0')->select();
            $this->assign('parents', $catelist);
            $this->display();
        } else {
            $goods = D('goods');
            $data['goods_name'] = $_POST['goods_name'];
            $data['cate_id'] = implode(",",$_POST['cate_id']);
            $data['unit'] = $_POST['unit'];
            $data['price'] = $_POST['price'];
            $data['discount_price'] = $_POST['discount_price'];
            $data['tag'] = $_POST['tag'];
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 0;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
            $upload->rootPath = './Uploads/'; // 设置附件上传目录
            $upload->saveName = date('Ymd') . md5(rand(999, 10000));
            $info = $upload->upload();
            if (!$info) {
                $this->error($upload->getError());
            } else {
                $image = new \Think\Image();
                $image->open('./Uploads/' . $info['file']['savepath'] . $info['file']['savename']);
                $image->thumb(120, 120, \Think\Image::IMAGE_THUMB_FIXED)->save('./Uploads/' . $info['file']['savepath'] . 'sthumb120_' . $info['file']['savename']);
                $data['goods_img'] = 'Uploads/'.$info['file']['savepath'].'sthumb120_'.$info['file']['savename'];
                $cateadd = $goods->add($data);
                if ($cateadd) {
                    $this->success('添加成功', 'index');
                } else {
                    $this->error('添加失败');
                }
            }
        }
    }
    public function edit(){
        if (!IS_POST) {
            $goods = D('goods');
            $cate = D('category');
            $goods_id=$_GET['goods_id'];
            $cate_name='';
            $list=$goods->where('goods_id='.$goods_id)->find();
            $cate_ids=explode(',',$list['cate_id']);
            foreach($cate_ids as $k=>$v){
                $name=$cate->where('cate_id='.$cate_ids[$k])->getField('cate_name');
                if($name){
                    $cate_name=$cate_name.$name.' / ';
                }
            }
            $list['cate_name']=substr($cate_name, 0,-2);
            $this->assign('goods',$list);
            $catelist = $cate->where('parent_id=0')->select();
            $this->assign('parents', $catelist);
            $this->display();
        }else{
            $goods_id=$_POST['goods_id'];
            $data['goods_name']=$_POST['goods_name'];
            $data['tag']=$_POST['tag'];
            $data['unit']=$_POST['unit'];
            $data['price']=$_POST['price'];
            $data['discount_price']=$_POST['discount_price'];
            $data['if_show']=$_POST['if_show'];
            $issel=true;
            $cate_ids=$_POST['cate_id'];
            if($cate_ids[0]==0){
                $issel=false;
            }
            if($issel){
                $data['cate_id']=implode(",", $cate_ids);
            }
            if($_FILES['file']['error']==4){
                $goods = D('goods');
                $goods_add1=$goods->where('goods_id='.$goods_id)->save($data);
                if ($goods_add1) {
                    $this->success('修改成功', 'index');
                } else {
                    $this->error('修改失败','index');
                }
            }else{
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 0;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
                $upload->rootPath = './Uploads/'; // 设置附件上传目录
                $upload->saveName = date('Ymd') . md5(rand(999, 10000));
                $info = $upload->upload();
                if (!$info) {
                    $this->error($upload->getError());
                } else {
                    $image = new \Think\Image();
                    $image->open('./Uploads/' . $info['file']['savepath'] . $info['file']['savename']);
                    $image->thumb(120, 120, \Think\Image::IMAGE_THUMB_FIXED)->save('./Uploads/' . $info['file']['savepath'] . 'sthumb120_' . $info['file']['savename']);
                    $image->thumb(60, 60, \Think\Image::IMAGE_THUMB_FIXED)->save('./Uploads/' . $info['file']['savepath'] . 'sthumb60_' . $info['file']['savename']);
                    $data['goods_img'] = 'Uploads/'.$info['file']['savepath'].'sthumb120_'.$info['file']['savename'];
                    $goods = D('goods');
                    $goods_add2=$goods->where('goods_id='.$goods_id)->save($data);
                    if ($goods_add2) {
                        $this->success('修改成功', 'index');
                    } else {
                        $this->error('修改失败','index');
                    }
                }
            }
        }
    }
    public function del(){
        $goods = D("goods");
        $goods_id=$_POST['id'];
        $delete = $goods->where('goods_id=' . $goods_id)->delete();
        if ($delete) {
            $arr['status'] = 1;
            $arr['msg'] = "删除成功";
        } else {
            $arr['status'] = 0;
            $arr['msg'] = "删除失败";
        }
        $this->ajaxReturn($arr);
    }
    public function delall(){
        $goods_id=$_POST['id'];
        $goods_id=explode(',',$goods_id);
        $isdel=false;
        $goods = D("goods");
        for($i=0;$i<count($goods_id);$i++){
            $delete = $goods->where('goods_id=' . $goods_id[$i])->delete();
            if($delete){
                $isdel=true;
            }else{
                $isdel=false;
            }
        }
        if($isdel){
            $arr ['status']=1;
            $arr['msg']="删除成功";
        }else{
            $arr ['status']=0;
            $arr['msg']="删除失败";
        }
        $this->ajaxReturn($arr);
    }
    public function ajax_cate(){
        $cate = D("category");
        $list = $cate->where('parent_id='.$_GET['pid'])->select();
        foreach ($list as $key => $val){
            $list[$key]['cate_name']= htmlspecialchars($val['cate_name']);
        }
        $this->ajaxReturn(array_values($list));
    }
}