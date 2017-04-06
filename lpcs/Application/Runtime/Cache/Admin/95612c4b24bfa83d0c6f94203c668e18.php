<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"><head>

    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7 charset=utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title> ECMall </title>
    <link href="/lpcs/Public/css/admin.css" rel="stylesheet" type="text/css">
    <style type="text/css">
        <!--
        body {background: #fcfdff}
        -->
    </style>
<body>
<div id="rightTop">
    <p>商品分类</p>
    <ul class="subnav">
        <li><a class="btn1" href="<?php echo U('category/index');?>">管理</a></li>
        <li><span>新增</span></li>
    </ul>
</div>
<div class="info">
    <form method="post" enctype="multipart/form-data" id="gcategory_form" action="<?php echo U('category/add');?>">
        <table class="infoTable">
            <tbody><tr>
                <th class="paddingT15">
                    分类名称:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="cate_name" type="text" name="cate_name" value=""> <label class="field_notice">分类名称</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">
                    <label for="parent_id">上级分类:</label></th>
                <td class="paddingT15 wordSpacing5">
                    <select id="parent_id" name="parent_id" style="height:23px"><option value="0">请选择...</option>
                        <?php if(!empty($parents)): if(is_array($parents)): foreach($parents as $key=>$item): ?><option value="<?php echo ($item["cate_id"]); ?>">
                                    <?php echo ($item["name"]); ?>
                                </option><?php endforeach; endif; endif; ?>
                    </select> <label class="field_notice">上级分类</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">
                    排序:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="sort_order" id="sort_order" type="text" name="sort_order" value="255">  <label class="field_notice">更新排序</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">显示:</th>
                <td class="paddingT15 wordSpacing5"><p>
                    <label>
                        <input type="radio" name="if_show" value="1" checked="checked">
                        是</label>
                    <label>
                        <input type="radio" name="if_show" value="0">
                        否</label> <label class="field_notice">新增的分类名称是否显示</label>
                </p></td>
            </tr>

            <tr>
                <th></th>
                <td class="ptb20">
                    <input class="formbtn" type="submit" name="Submit" value="提交">
                    <input class="formbtn" type="reset" name="reset" value="重置">            </td>
            </tr>
            </tbody></table>
    </form>
</div>
<p id="page_footer">Copyright 2003-2012 ShopEx Inc.,All rights reserved.</p>
</body></html>