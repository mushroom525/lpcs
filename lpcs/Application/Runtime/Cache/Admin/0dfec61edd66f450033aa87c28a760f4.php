<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>良品菜市后台登录</title>

    <link rel="stylesheet" type="text/css" href="/lpcs/Public/css/styles.css">

</head>
<body>


<div class="wrapper">

    <div class="container">
        <h1>Welcome</h1>
        <form class="form">
            <input type="text" placeholder="用户名" name="name" id="name">
            <input type="password" placeholder="密码" name="pwd" id="pwd">
            <button type="submit" id="login-button">登录</button>
        </form>
        <h4 id="tip" style="color:red;display:none">用户名或密码不正确！</h4>
    </div>

    <ul class="bg-bubbles">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>

</div>

<script type="text/javascript" src="/lpcs/Public/js/jquery1.9.0.min.js"></script>
<script type="text/javascript">
    $('#login-button').click(function(event){
        event.preventDefault();
        var name=$('#name').val();
        var pwd=$('#pwd').val();
        $.post("<?php echo U('ajaxlogin');?>",{name:name,pwd:pwd},function(data){
            if(data.status==1){
                $('form').fadeOut(500);
                $('#tip').fadeOut(500);
                $('.wrapper').addClass('form-success');
                function location(){ window.location.href="http://www.heeyhome.com/lpcs/admin";};
                setTimeout(function(){
                    location();
                },1500);
            }else{
                $('#tip').css('display','');
                setTimeout(function(){$('#tip').css('display','none')},1000);
            }
        });
    });
</script>
</body>
</html>