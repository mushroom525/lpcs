<?php
ini_set('date.timezone','Asia/Shanghai');
header("Content-Type: text/html;charset=utf8");
//error_reporting(E_ERROR);
require_once "../lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

//打印输出数组信息
function printf_info($data)
{
    foreach($data as $key=>$value){
echo "<font color='#00ff55;'>$key</font> : $value <br/>";
}
}

//①、获取用户openid
$tools = new JsApiPay();
$openId = $tools->GetOpenid();

//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody("test");
$input->SetAttach("test");
$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
$input->SetTotal_fee("1");
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag("test");
$input->SetNotify_url("http://paysdk.weixin.qq.com/example/notify.php");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
$order = WxPayApi::unifiedOrder($input);
echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
printf_info($order);
$jsApiParameters = $tools->GetJsApiParameters($order);
//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
/**
* 注意：
* 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
* 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
* 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"
          content="width=device-width,initial-scale=1.0,minmum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <title></title>
  <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/order.css">
</head>
<body>

<nav class="navbar navbar-fixed-top">
    <div class="container title">
        <i class="go_back"></i>
        <span class="order_span">确认订单</span>
        <i></i>
    </div>
</nav>
<div class="address_wrap order_address" data-id="1">
    <div class="address_detail clearfix">
        <i class="fl"></i>
        <div class="address fl">
            <p>
                <span class="address_name"></span>
                <span class="address_tel"></span>
            </p>
            <p class="address_room"></p>
        </div>
    </div>
    <div class="manage_address">
        <i></i>
    </div>
</div>
<div class="time_wrap">
    <div class="time_left">
        <i></i>
        <span>预计送达时间</span>
    </div>
    <div class="time_right Jsongda">
        <!--<span class="today">今天</span>-->
        <!--<span>PM 17:00</span>-->
        <span>尽快送达</span>
        <i></i>
    </div>
</div>
<div class="shop_name">
    <i></i>
    <span>良品菜市</span>
</div>
<div class="shop_content">
    <ul>
        <!--<li>-->
        <!--<span class="v_name">胡萝卜</span>-->
        <!--<span class="v_num">x 1</span>-->
        <!--<span class="v_price">￥12.00</span>-->
        <!--</li>-->
        <!--<li>-->
        <!--<span class="v_name">大葱</span>-->
        <!--<span class="v_num">x 6</span>-->
        <!--<span class="v_price">￥12.00</span>-->
        <!--</li>-->
        <!--<li>-->
        <!--<span class="v_name">苏太猪肉</span>-->
        <!--<span class="v_num">x 5</span>-->
        <!--<span class="v_price">￥10.00</span>-->
        <!--</li>-->
        <!--<li>-->
        <!--<span class="v_name">胡萝卜</span>-->
        <!--<span class="v_num">x 1</span>-->
        <!--<span class="v_price">￥12.00</span>-->
        <!--</li>-->
        <!--<li>-->
        <!--<span class="v_name">大葱</span>-->
        <!--<span class="v_num">x 6</span>-->
        <!--<span class="v_price">￥12.00</span>-->
        <!--</li>-->
        <!--<li>-->
        <!--<span class="v_name">苏太猪肉</span>-->
        <!--<span class="v_num">x 5</span>-->
        <!--<span class="v_price">￥10.00</span>-->
        <!--</li>-->
    </ul>
</div>
<div class="fee">
    <span class="v_name fee_span">配送费</span>
    <span class="v_num"></span>
    <span class="v_price">￥10.00</span>
</div>
<div class="order_mark">
    <p>订单备注</p>
    <textarea title="" placeholder="若有其他需要请留言"></textarea>
</div>
<nav class="navbar navbar-fixed-bottom">
    <div class="container gwc_bottom">
        <div class="order_money">
            <i>合计：</i>
            <span>￥135.00</span>
        </div>
        <div class="order_pay">
            <a href="javascript:;" onclick="callpay()">确认付款</a>
        </div>
    </div>
</nav>
<section id="time" class="navbar navbar-fixed-bottom">
    <div>
        <div id="wrap" class="bj display"></div>
        <div id="Jcontainer" class="container display">
            <div id="Jtime_top" class="time_top">选择送达时间</div>
            <div class="scroll-holder">
                <div id="Jleft_slide" class="col-xs-3 col-sm-3 left_slide">
                    <ul>
                        <!--<li class="active">今日</li>
                        <li>明日</li>-->
                    </ul>
                </div>
                <div id="J_scroll_holder" class="col-xs-9 col-sm-9 right_content">
                    <ul data-time="">
                        <!--<li>
                            <p>尽快送达（预计15:22）</p>	<i class="click"></i>
                        </li>
                        <li><p>16:00</p><i></i></li>
                        <li><p>16:15</p><i></i></li>
                        <li><p>16:30</p><i></i></li>
                        <li><p>16:45</p><i></i></li>
                        <li><p>16:00</p><i></i></li>
                        <li><p>16:15</p><i></i></li>
                        <li><p>16:30</p><i></i></li>
                        <li><p>16:45</p><i></i></li>-->
                    </ul>
                    <input id="JselectIt" type="hidden" value=""/>
                </div>
            </div>
        </div>
    </div>

</section>
</body>
</html>
<script src="../js/jquery-1.11.3.min.js"></script>
<script src="../js/scrollfix.js" type="text/javascript" charset="utf-8"></script>
<script src="../js/order.js"></script>
<script type="text/javascript">
    //调用微信JS api 支付
    function jsApiCall() {
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest',
//        <?php echo $jsApiParameters; ?>,
            function (res) {
                WeixinJSBridge.log(res.err_msg);
                alert(res.err_code + res.err_desc + res.err_msg);
            }
        );
    }
    function callpay() {
        if (typeof WeixinJSBridge == "undefined") {
            if (document.addEventListener) {
                document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
            } else if (document.attachEvent) {
                document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
            }
        } else {
            jsApiCall();
        }
    }
</script>

<!--<script>-->
<!--var scrollable = document.getElementById("J_orderContent");-->
<!--new ScrollFix(scrollable);-->

<!--var scrollable1 = document.getElementById("J_scroll_holder");-->
<!--new ScrollFix(scrollable1);-->
<!--document.getElementById('J_navbar').addEventListener('touchmove', function (e) {-->
<!--e.preventDefault();-->
<!--}, false);-->

<!--document.getElementById('J_top').addEventListener('touchmove', function (e) {-->
<!--e.preventDefault();-->
<!--}, false);-->
<!--document.getElementById('wrap').addEventListener('touchmove', function (e) {-->
<!--e.preventDefault();-->
<!--}, false);-->
<!--document.getElementById('Jtime_top').addEventListener('touchmove', function (e) {-->
<!--e.preventDefault();-->
<!--}, false);-->
<!--document.getElementById('Jleft_slide').addEventListener('touchmove', function (e) {-->
<!--e.preventDefault();-->
<!--}, false);-->

<!--</script>-->