<?php if (!defined('THINK_PATH')) exit();?><html xmlns="http://www.w3.org/1999/xhtml"><head>

    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7 charset=utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title> ECMall </title>
    <link href="/lpcs/Public/css/admin.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="/lpcs/Public/js/jquery1.9.0.min.js"></script>
    <script type="text/javascript" src="/lpcs/Public/js/ajax_tree.js"></script>
    <script type="text/javascript" charset="utf-8" src="/lpcs/Public/layer/layer.js"></script>
    <style type="text/css">
        <!--
        body {background: #fcfdff}
        -->
    </style>
    <link rel="stylesheet" type="text/css" href="/lpcs/Public/css/jqtreetable.css"></head>
<body>
<div id="rightTop">
    <p>商品分类</p>
    <ul class="subnav">
        <li><span>管理</span></li>
        <li><a class="btn1" href="<?php echo U('category/add');?>">新增</a></li>
    </ul>
</div>

<div class="info2">
    <table class="distinction" style="font-size: 12px">
        <thead>
        <tr class="tatr1">
            <td class="w30"></td>
            <td width="40%">分类名称</td>
            <td>排序</td>
            <td>显示</td>
            <td class="handler">操作</td>
            <td></td>
            <td></td>
        </tr>
        </thead>
        <tbody id="treet1">
        <?php if(!empty($list)): if(is_array($list)): foreach($list as $key=>$item): ?><tr style="background: rgb(255, 255, 255);">
            <td class="align_center w30"><input type="checkbox" class="checkitem" value="<?php echo ($item["cate_id"]); ?>" name="id[]"></td>
            <td class="node" width="50%"><img src="/lpcs/Public/images/tv-expandable.gif" ectype="flex" status="open" fieldid="<?php echo ($item["cate_id"]); ?>"><span class="node_name" ectype="inline_edit" fieldname="cate_name" fieldid="<?php echo ($item["cate_id"]); ?>" required="1"><?php echo ($item["cate_name"]); ?></span></td>
            <td class="align_center"><span ectype="inline_edit" fieldname="sort_order" fieldid="<?php echo ($item["cate_id"]); ?>" datatype="number"><?php echo ($item["sort_order"]); ?></span></td>
            <td class="align_center"><?php if($item['if_show'] == 1): ?><img src="/lpcs/Public/images/positive_enabled.gif" ectype="inline_edit" fieldname="if_show" fieldid="<?php echo ($item["cate_id"]); ?>" fieldvalue="1"/><?php else: ?><img src="/lpcs/Public/images/positive_disabled.gif" ectype="inline_edit" fieldname="if_show" fieldid="<?php echo ($item["cate_id"]); ?>" fieldvalue="1"></td><?php endif; ?>
            <td class="handler"><span><a href="/lpcs/Admin/category/edit.html?cate_id=<?php echo ($item['cate_id']); ?>">编辑</a>
                |
                <a href="javascript:void(0)" onclick="del(<?php echo ($item['cate_id']); ?>)">删除</a> | <a href="<?php echo U('category/add');?>">新增下级</a></span>
            </td>
            <td width="5%"></td>
            <td></td>
        </tr><?php endforeach; endif; endif; ?>
        </tbody>        <tfoot>
    <tr class="tr_pt10">
        <td class="align_center"><label for="checkall1"><input id="checkall" type="checkbox" class="checkall" ></label></td>
        <td colspan="4" id="batchAction">
            <span class="all_checkbox"><label for="checkall">全选</label></span>&nbsp;&nbsp;
            <input class="formbtn batchButton" type="button" value="删除" onclick="DelSelect()">
        </td>
        <td></td>
        <td></td>
    </tr>
    </tfoot>
    </table>
</div>
<script type="text/javascript">
    //单个删除
    function del(id){
        layer.confirm('您确定要删除吗？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            $.post("<?php echo U('del');?>",{id:id},function(data){
                if(data.status==1){
                    layer.msg(data.msg,{icon :6});
                    function location(){ window.location.reload();};
                    setTimeout(function(){
                        location();
                    },1500);
                }else if(data.status==2){
                    layer.msg(data.msg,{icon :5});
                }
                else{
                    layer.msg(data.msg,{icon :5});
                }
            });
        }, function(){

        });
    }
    //全选
    $("#checkall").click(function(){
        $("input[name='id[]']").each(function(){
            if (this.checked) {
                this.checked = false;
            }
            else {
                this.checked = true;
            }
        });
    });
    //批量删除
    function DelSelect() {
        var a=new Array();
        var i=0;
        var Checkbox = false;
        $("input[name='id[]']").each(function () {
            if (this.checked == true) {
                a[i]=this.value;
                ++i;
                Checkbox = true;
            }
        });
        if (Checkbox) {
            var idlist=a.join(',');
            layer.confirm('删除该分类将会同时删除该分类的所有下级分类，您确定要删除吗？', {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.post("<?php echo U('delall');?>",{id:idlist},function(data){
                    if(data.status==1){
                        layer.msg(data.msg,{icon :6});
                        function location(){ window.location.reload();};
                        setTimeout(function(){
                            location();
                        },1500);
                    }else{
                        layer.msg(data.msg,{icon :5});
                    }
                });
            }, function(){
            });
        }
        else
        {
            layer.msg('请选择您要删除的内容！');
            return false;
        }
    }
    </script>
</body></html>