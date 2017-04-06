<?php
namespace Admin\Controller;
class CategoryController extends CommonController  {
    public function index(){
        $cate = D('category');
        $list=$cate->where('parent_id=0')->select();
        $this->assign('list',$list);
        $this->display();
    }
    public function add()
    {
        if (!IS_POST) {
            /* 参数 */
            $pid = empty($_GET['pid']) ? 0 : intval($_GET['pid']);
            $gcategory = array('parent_id' => $pid, 'sort_order' => 255, 'if_show' => 1);
            $this->assign('gcategory', $gcategory);
            $cate = D('category');
            $catelist = $cate->field(array('cate_id', 'cate_name', 'parent_id', 'child_id', "concat(child_id,cate_id) as fullpath"))->order('fullpath asc')->select();
            foreach ($catelist as $key => $value) {
                $catelist[$key]['deep'] = explode(',', $value['fullpath']);
                if($value['parent_id']==0){
                    $catelist[$key]['name'] = $catelist[$key]['cate_name'];
                }else
                $catelist[$key]['name'] = htmlspecialchars_decode(str_repeat('-', count($catelist[$key]['deep'])) . $catelist[$key]['cate_name']);
            }
            //dump($catelist);die;
            $this->assign('parents', $catelist);
            $this->display();
        } else {
            $cate = D('category');
            $data['cate_name'] = $_POST['cate_name'];
            $data['parent_id'] = $_POST['parent_id'];
            if ($data['parent_id'] != 0) {
                $child_id = $cate->where('cate_id=' . $data['parent_id'])->getField('child_id');
                $data['child_id'] = $child_id.$data['parent_id'].',';
            } else {
                $data['child_id'] = '0,';
            }
            $cateadd = $cate->add($data);
            if ($cateadd) {
                $this->success('添加成功', 'index');
            } else {

                $this->error('添加失败');

            }
        }
    }
    public function edit(){
        if (!IS_POST) {
            $cate_id = $_GET['cate_id'];
            $cate=D('category');
            $info = $cate->find($cate_id);
            $info['parent']=$cate->where('cate_id='.$info['parent_id'])->getField('cate_name');
            $this->assign('info',$info);
            $this->display();
        }
        else{
            $cate=D('category');
            $cate_id=$_POST['cate_id'];
            $cate->where('cate_id='.$cate_id)->save($_POST);
            $this->success('修改成功','index');
        }
    }
    public function ajax_cate(){
        $cate = D("category");
        $list = $cate->where('parent_id='.$_GET['id'])->select();
        foreach ($list as $key => $val)
        {
            $child=$cate->where('parent_id='.$val['cate_id'])->select();
            if (!$child || empty($child) )
            {

                $list[$key]['switchs'] = 0;
            }
            else
            {
                $list[$key]['switchs'] = 1;
            }
        }
        $this->ajaxReturn($list);
    }
    public function del(){
        $cate = D("category");
        $cate_id=$_POST['id'];
        $list = $cate->where('parent_id='.$cate_id)->select();
        if(!empty($list)){
            $arr['status']=2;
            $arr['msg']="该分类下有子类，不能删除！";
        }else {
            $delete = $cate->where('cate_id=' . $cate_id)->delete();
            if ($delete) {
                $arr['status'] = 1;
                $arr['msg'] = "删除成功";
            } else {
                $arr['status'] = 0;
                $arr['msg'] = "删除失败";
            }
        }
        $this->ajaxReturn($arr);
    }
    public function delall(){
        $cate_id=$_POST['id'];
        $cate_id=explode(',',$cate_id);
        $isdel=false;
        $cate = D("category");
        for($i=0;$i<count($cate_id);$i++){
            $delete1 = $cate->where('parent_id=' . $cate_id[$i])->delete();
            $delete2 = $cate->where('cate_id=' . $cate_id[$i])->delete();
            if($delete2){
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
}