<?php if (!defined('THINK_PATH')) exit();?><html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7 charset=utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title> ECMall </title>
    <link href="/lpcs/Public/css/admin.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="/lpcs/Public/js/jquery1.9.0.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="/lpcs/Public/layer/layer.js"></script>
    <style type="text/css">
        <!--
        body {background: #fcfdff}
        -->
    </style>
    </head>
<body>
<div id="rightTop">
    <p>商品管理</p>
    <ul class="subnav">
        <li><span>管理</span></li>
        <li>
            <a class="btn1" href="<?php echo U('goods/add');?>">新增</a>
        </li>
    </ul>
</div>

<div class="tdare">
    <table width="100%" cellspacing="0" class="dataTable" style="font-size:12px">
        <tbody><tr class="tatr1">
            <td width="5%" class="firstCell"></td>
            <td width="5%"><span ectype="order_by" fieldname="goods_name">商品图片</span></td>
            <td width="15%"><span ectype="order_by" fieldname="store_name">商品名</span></td>
            <td width="10%"><span ectype="order_by" fieldname="cate_id"></span></td>
            <td width="10%"><span ectype="order_by" fieldname="cate_id">分类</span></td>
            <td width="7%"><span ectype="order_by" fieldname="if_show">上架</span></td>
            <td width="10%"><span ectype="order_by" fieldname="views">销售价</span></td>
            <td width="8%"><span ectype="order_by" fieldname="views">折扣价</span></td>
            <td width="8%"><span ectype="order_by" fieldname="views">单位</span></td>
            <td width="8%"><span ectype="order_by" fieldname="views">标签</span></td>
            <td>操作</td>
        </tr>
        <?php if(!empty($goods)): if(is_array($goods)): foreach($goods as $key=>$item): ?><tr class="tatr2">
            <td class="firstCell"><input type="checkbox" class="checkitem" value="<?php echo ($item["goods_id"]); ?>" name='id[]'></td>
            <td><span><img src="/lpcs/<?php echo ($item["goods_img"]); ?>" width="50px" height="50px"/></span></td>
            <td><?php echo ($item["goods_name"]); ?></td>
            <td class="align_center"><td><?php echo (htmlentities($item["cate_name"])); ?></td></td>
            <td class="table-center"><?php if($item['if_show'] == 1): ?><img src="/lpcs/Public/images/positive_enabled.gif" ectype="inline_edit" fieldname="if_show" /><?php else: ?><img src="/lpcs/Public/images/positive_disabled.gif" ectype="inline_edit" fieldname="if_show"><?php endif; ?></td>
            <td><?php echo ($item["price"]); ?></td>
            <td><?php echo ($item["discount_price"]); ?></td>
            <td><?php echo ($item["unit"]); ?></td>
            <td><?php echo ($item["tag"]); ?></td>
            <td class="handler"><span><a href="<?php echo U('edit?goods_id='.$item['goods_id']);?>">编辑</a>
                |
                <a href="javascript:void(0)" onclick="del(<?php echo ($item['goods_id']); ?>)">删除</a> </span>
            </td>
        </tr><?php endforeach; endif; endif; ?>
        </tbody></table>
    <div id="dataFuncs">
        <div id="batchAction" class="left paddingT15"><label for="checkall1" style="padding-left:49px"><input id="checkall" type="checkbox" class="checkall" ></label>&nbsp;&nbsp;<label for="checkall">全选</label>&nbsp;&nbsp;
            <input class="formbtn batchButton" type="button" value="删除" name="id" onclick="DelSelect()">
        </div>
        <div class="pageLinks"></div>
    </div>
    <div class="clear"></div>
</div>
<p id="page_footer">Copyright 2003-2012 ShopEx Inc.,All rights reserved.</p>
<script type="text/javascript">
    //单个删除
    function del(id){
        layer.confirm('您确定要删除该商品吗？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            $.post("<?php echo U('del');?>",{id:id},function(data){
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
            layer.confirm('您确定要删除该商品吗？', {
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