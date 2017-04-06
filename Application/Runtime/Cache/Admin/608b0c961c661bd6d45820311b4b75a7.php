<?php if (!defined('THINK_PATH')) exit();?><html xmlns="http://www.w3.org/1999/xhtml"><head>
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7 charset=utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title> ECMall </title>
    <link href="/wxmall/Public/css/admin.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="/wxmall/Public/js/jquery1.9.0.min.js"></script>
    <script type="text/javascript" src="/wxmall/Public/js/mlselection.js"></script>
    <style type="text/css">
        <!--
        body {background: #fcfdff}
        -->
    </style>
    <script type="text/javascript">
        //<!CDATA[
        $(function(){
            // multi-select mall_gcategory
            $('#gcategory').length>0 && gcategoryInit("gcategory");
        });
        //]]>
    </script>
    </head>
<body>
<div id="rightTop">
    <p>商品管理</p>
    <ul class="subnav">
        <li><a class="btn1" href="<?php echo U('goods/index');?>">管理</a></li>
        <li>
            <a class="btn1" href="<?php echo U('goods/add');?>">新增</a>
        </li>
    </ul>
</div>
<div class="info">
    <form method="post" enctype="multipart/form-data" id="user_form" action="<?php echo U('goods/edit');?>">
        <table class="infoTable">
            <tbody><tr>
                <th class="paddingT15"> 商品名:</th>
                <td class="paddingT15 wordSpacing5">
                    <input type="hidden" name="goods_id" value="<?php echo ($goods["goods_id"]); ?>">
                    <input class="infoTableInput2" id="user_name" type="text" name="goods_name" value="<?php echo ($goods["goods_name"]); ?>">
                </td>
            </tr>
            <tr>
                <th class="paddingT15">商品图片:</th>
                <td class="paddingT15 wordSpacing5"><input class="infoTableFile2" type="file" name="file" id="portrait">
                    <label class="field_notice">支持格式gif,jpg,jpeg,png</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">
                    <label for="parent_id">商品分类:</label></th>
                <td class="paddingT15 wordSpacing5" id="gcategory">
                    <label id="dis" style="display:inline"><?php echo ($goods["cate_name"]); ?></label>
                    <span id="editable" class="editable" title="可编辑" style="display: inline;"></span>
                    <select name="cate_id[]" style="height:23px;display: none" id="play"><option value="0">请选择...</option>
                        <?php if(!empty($parents)): if(is_array($parents)): foreach($parents as $key=>$item): ?><option value="<?php echo ($item["cate_id"]); ?>">
                                    <?php echo ($item["cate_name"]); ?>
                                </option><?php endforeach; endif; endif; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">商品标签:</th>
                <td class="paddingT15 wordSpacing5"><p>
                    <label>
                        <input type="radio" name="tag" value="0" <?php echo ($goods['tag']==0?'checked':''); ?>>
                        无标签</label>
                    <label>
                        <input type="radio" name="tag" value="1" <?php echo ($goods['tag']==1?'checked':''); ?>>
                        【今日推荐】</label>
                    <label>
                        <input type="radio" name="tag" value="2" <?php echo ($goods['tag']==2?'checked':''); ?>>
                        【特惠供应】</label>
                </p></td>
            </tr>

            <tr>
                <th class="paddingT15"> 单位:</th>
                <td class="paddingT15 wordSpacing5"><input class="infoTableInput2" name="unit" type="text"  value="<?php echo ($goods["unit"]); ?>" style="width: 50px">        </td>
            </tr>
            <tr>
                <th class="paddingT15"> 售价:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" name="price" type="text" id="price" value="<?php echo ($goods["price"]); ?>" style="width: 90px">
                </td>
            </tr>
            <tr>
                <th class="paddingT15"> 折扣价:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" name="discount_price" type="text" id="discount_price" value="<?php echo ($goods["discount_price"]); ?>" style="width: 90px">
                    <label class="field_notice">没有默认为0</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">上架:</th>
                <td class="paddingT15 wordSpacing5"><p>
                    <label>
                        <input type="radio" name="if_show" value="1" <?php echo ($goods['if_show']==1?'checked':''); ?>>
                        是</label>
                    <label>
                        <input type="radio" name="if_show" value="0" <?php echo ($goods['if_show']==0?'checked':''); ?>>
                        否</label>
                </p></td>
            </tr>
            <tr>
                <th></th>
                <td class="ptb20"><input class="formbtn" type="submit" name="Submit" value="提交">
                    <input class="formbtn" type="reset" name="Reset" value="重置" onclick="myrefresh()">
                </td>
            </tr>
            </tbody></table>
    </form>
</div>
<p id="page_footer">Copyright 2003-2012 ShopEx Inc.,All rights reserved.</p>
<script>
    $('#editable').click(function(){
        $('#dis').css('display','none');
        $(this).css('display','none');
        $('#play').css('display','');
    });
    function myrefresh()
    {
        window.location.reload();
    }
</script>
</body></html>