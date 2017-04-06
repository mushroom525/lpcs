<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>厚生网后台管理</title>
<link rel="stylesheet" href="/wxmall/Public/css/bootstrap.css" />
<link rel="stylesheet" href="/wxmall/Public/css/css.css" />
<script type="text/javascript" src="/wxmall/Public/js/jquery1.9.0.min.js"></script>
<script type="text/javascript" src="/wxmall/Public/js/bootstrap.min.js"></script>
</head>

<body>
<div class="header">
     <div class="logo"></div>
     
                <div class="header-right">
                <i class="icon-user icon-white"></i> <a style="text-decoration:none;">您好 <?php echo ($_SESSION['login']['type']==0?超级管理员:普通管理员); ?>：<?php echo ($_SESSION['login']['name']); ?></a> 
                <i class="icon-off icon-white"></i> <a href="<?php echo U('Login/logout');?>" target="_parent">注销</a>
                </div>
</div>
<!-- 顶部 -->     

</body>
</html>