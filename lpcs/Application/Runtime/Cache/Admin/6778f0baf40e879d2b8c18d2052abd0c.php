<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>学工网后台管理系统</title>
<link rel="stylesheet" href="/lpcs/Public/css/bootstrap.css" />
<link rel="stylesheet" href="/lpcs/Public/css/css.css" />
<script type="text/javascript" src="/lpcs/Public/js/jquery1.9.0.min.js"></script>
<script type="text/javascript" src="/lpcs/Public/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/lpcs/Public/js/sdmenu.js"></script>
</head>

<body>
            
<div id="middle">
     <div class="left">
         <script type="text/javascript">
var myMenu;
window.onload = function() {
    myMenu = new SDMenu("my_menu");
    myMenu.init();
};
</script>

<div id="my_menu" class="sdmenu">
    <div >
        <span>菜单列表</span>
        <a href="<?php echo U('category/index');?>" target="main">分类管理</a>
        <a href="<?php echo U('goods/index');?>" target="main">商品管理</a>
        <a  target="main" target="main">订单管理</a>
    </div>
</div>
     </div>
     <div class="Switch"></div>
    </div>
<script>
    $('.sdmenu div a').click(function(){
        $('.sdmenu div a').removeClass('current');
        $(this).addClass('current');
    });
</script>
</body>
</html>