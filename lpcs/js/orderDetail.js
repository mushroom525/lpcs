/**
 * Created by Administrator on 2017/3/30.
 */
var BASEURL = 'http://www.heeyhome.com/lpcs/home/';
var ORDERINFOURL = BASEURL + 'order/orderinfo'; // 订单详情
var ORDERCANCELURL = BASEURL + 'order/ordercancel'; // 取消订单
var ORDERPAYURL = BASEURL + 'order/order_continue'; // 付款
var AGAINORDERURL = BASEURL + 'order/orderagain'; // 再来一单


var path = 'http://www.heeyhome.com/lpcs/view/';///untitled

var openid = sessionStorage.getItem('openid');//sessionStorage.getItem('openid')

var MSG1 = '支付成功！';
var MSG2 = '等待商家接单...';
var MSG3 = '已接单！';
var MSG4 = '等待商家配送...';
var MSG5 = '订单已完成！';
var MSG6 = '待支付！';
var MSG7 = '<span>15分钟</span>后订单将自动取消';
var MSG8 = '订单已取消！';

var orderDetailEv = {
    init: function () {
        var self = this;
        self.goBackEvent();
        self.orderDetailInfo();

    },
    /**
     * 返回上一页
     */
    goBackEvent: function () {
        $('.go_back').click(function () {
            window.location.href = path + 'orderList.html';
        })
    },
    /**
     * 倒计时
     */
    countdownEvent: function (orderDetailId) {
        var intDiff = parseInt(900); //倒计时总秒数量
        function timer(intDiff) {
            window.setInterval(function () {
                var day = 0,
                    hour = 0,
                    minute = 0,
                    second = 0; //时间默认值
                if (intDiff > 0) {
                    day = Math.floor(intDiff / (60 * 60 * 24));
                    hour = Math.floor(intDiff / (60 * 60)) - (day * 24);
                    minute = Math.floor(intDiff / 60) - (day * 24 * 60) - (hour * 60);
                    second = Math.floor(intDiff) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
                } else {
                    // orderDetailEv.cancelOrder(orderDetailId);
                }
                if (minute <= 9) minute = '0' + minute;
                if (second <= 9) second = '0' + second;
                $('.status_top span span').html(minute + '分' + second + '秒');
                intDiff--;
            }, 1000);
        }

        $(function () {
            timer(intDiff);
        });
    },
    /**
     * 取消订单
     */
    cancelOrder: function (order_id) {
        $.ajax({
            url: ORDERCANCELURL,
            type: "GET",
            async: true,
            data: {
                order_id: order_id
            },
            dataType: 'jsonp',
            success: function (data) {
                if (data.code == '000') {
                    window.location.href = path + 'orderList.html';
                } else {
                    layer.msg(data.msg);
                }
            },
            error: function (data) {
            }
        });
    },
    /**
     * 再来一单
     */
    againOrder: function (order_id) {
        $.ajax({
            url: AGAINORDERURL,
            type: "GET",
            async: true,
            data: {
                order_id: order_id
            },
            dataType: 'jsonp',
            success: function (data) {
                if (data.code == '000') {
                    window.location.href = 'http://www.heeyhome.com/lpcs/index.html';
                } else {
                    layer.msg(data.msg);
                }
            },
            error: function (data) {
            }
        });
    },
    /**
     * 去付款
     */
    gotoPay: function (order_id) {
        $.ajax({
            url: ORDERPAYURL,
            type: "GET",
            async: true,
            data: {
                order_id: order_id
            },
            dataType: 'jsonp',
            success: function (data) {
                if (data.code == '000') {
                    var parsedata = JSON.parse(data.data.jsApiParameters);
                    var orderid = data.data.order_id;
                    callpay(parsedata, orderid);
                } else {
                    layer.msg(data.msg);
                }
            },
            error: function (data) {
            }
        });
    },
    /**
     * 获取订单详情内容
     */
    orderDetailInfo: function () {
        var orderDetailId = sessionStorage.getItem('orderDetailId');
        var $shopcontent = $('.shop_content ul');
        $.ajax({
            url: ORDERINFOURL,
            type: "GET",
            async: true,
            data: {
                order_id: orderDetailId
            },
            dataType: 'jsonp',
            success: function (data) {
                if (data.code == '000') {
                    console.log(data.data);
                    $.each(data.data.goods, function (i, v) {
                        var shopStr = '<li>';
                        shopStr += '<span class="v_name">' + v.goods_name + '</span>';
                        shopStr += '<span class="v_num">x ' + v.goods_num + '</span>';
                        shopStr += '<span class="v_price">￥' + v.discount_price + '</span>';
                        shopStr += '</li>';
                        $shopcontent.append(shopStr);
                    });
                    $('#psf i').html(data.data.distribution_cost);//配送费
                    $('#total i').html(data.data.total_amount);//总额
                    $('.order_num .list_right').html(data.data.order_id);//订单编号
                    $('.order_time .list_right').html(data.data.order_time);//订单时间
                    $('.goods_num .list_right').html(data.data.goods_num);//商品数量
                    $('.send_address .list_right').html(data.data.address + data.data.room);//配送地址
                    $('#remark').html(data.data.remark);//备注
                    if (data.data.order_step == '0') {//0:待支付
                        $('.status_top b').html(MSG6);
                        $('.status_top span').html(MSG7);
                        $('#order_detail').css('display', 'flex');
                        orderDetailEv.countdownEvent(orderDetailId);
                        $('.order_step').show();
                        $('.step_content .content1').html('待支付').removeClass('active');
                        $('.step_content .content2').html('待接单').removeClass('active');
                        $('.step_content .content3').html('待送达').removeClass('active');
                        $('.detail_pay').click(function () {//去付款
                            orderDetailEv.gotoPay(orderDetailId);
                        });
                        $('.cancel_order').click(function () {//取消订单
                            orderDetailEv.cancelOrder(orderDetailId);
                        });
                    } else if (data.data.order_step == '1') {//1：已支付待接单
                        $('.status_top i').css('display', 'inline-block');
                        $('.status_top b').html(MSG1);
                        $('.status_top span').html(MSG2);
                        $('#order_detail').css('display', 'flex');
                        $('.detail_pay').html('再来一单');
                        $('.order_step').show();
                        $('.step_content .content1').html('已支付').addClass('active');
                        $('.step_content .content2').html('待接单').removeClass('active');
                        $('.step_content .content3').html('待送达').removeClass('active');
                        $('.cancel_order').click(function () {//取消订单
                            orderDetailEv.cancelOrder(orderDetailId);
                        });
                        $('.detail_pay').click(function () {//再来一单
                            orderDetailEv.againOrder(orderDetailId);
                        });
                    } else if (data.data.order_step == '2') {//2：待配送
                        $('.status_top b').html(MSG3);
                        $('.status_top span').html(MSG4);
                        $('#order_again').show();
                        $('.order_step').show();
                        $('.step_content .content1').html('已支付').addClass('active');
                        $('.step_content .content2').html('已接单').addClass('active');
                        $('.step_content .content3').html('待送达').removeClass('active');
                        $('#order_again').click(function () {//再来一单
                            orderDetailEv.againOrder(orderDetailId);
                        });
                    } else if (data.data.order_step == '3') {//3：已完成
                        $('.status_top b').html(MSG5);
                        $('#order_again').show();
                        $('.order_step').show();
                        $('.step_content .content1').html('已支付').addClass('active');
                        $('.step_content .content2').html('已接单').addClass('active');
                        $('.step_content .content3').html('已送达').addClass('active');
                        $('#order_again').click(function () {//再来一单
                            orderDetailEv.againOrder(orderDetailId);
                        });
                    } else if (data.data.order_step == '4') {//4：已取消
                        $('.status_top b').html(MSG8);
                        $('#order_again').show();
                        $('#order_again').click(function () {//再来一单
                            orderDetailEv.againOrder(orderDetailId);
                        });
                    }
                } else {
                    layer.msg(data.msg);
                }
            },
            error: function (data) {
            }
        });
    }
};
/**
 * 微信支付
 */
function callpay(jsStr, orderid) {
    if (typeof WeixinJSBridge == "undefined") {
        if (document.addEventListener) {
            document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
        } else if (document.attachEvent) {
            document.attachEvent('WeixinJSBridgeReady', jsApiCall);
            document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
        }
    } else {
        jsApiCall(jsStr, orderid);
    }
}


//调用微信JS api 支付
function jsApiCall(jsStr, orderid) {

    WeixinJSBridge.invoke(
        'getBrandWCPayRequest',
        {
            "appId": jsStr.appId,
            "nonceStr": jsStr.nonceStr,
            "package": jsStr.package,
            "paySign": jsStr.paySign,
            "signType": jsStr.signType,
            "timeStamp": jsStr.timeStamp
        },
        function (res) {
            WeixinJSBridge.log(res.err_msg);
            if (res.err_msg == 'get_brand_wcpay_request:cancel') {
                layer.alert("您已取消了此次支付");
                window.location.href = path + 'orderList.html';
                return;
            } else if (res.err_msg == 'get_brand_wcpay_request:fail') {
                layer.alert("支付失败");
                window.location.href = path + 'orderList.html';
                return;
            } else if (res.err_msg == 'get_brand_wcpay_request:ok') {
                $.ajax({
                    url: ORDERSTEPURL,
                    type: "GET",
                    async: true,
                    data: {
                        order_id: orderid
                    },
                    dataType: 'jsonp',
                    success: function (data) {
                        if (data.code == '000') {
                            sessionStorage.setItem('orderDetailId', orderid);
                            window.location.href = path + 'orderDetail.html';
                        }
                    },
                    error: function (data) {
                    }
                });

            } else {
                layer.alert("未知错误" + res.error_msg);
                window.location.href = path + 'orderList.html';
                return;
            }
        }
    );
}
$(function () {
    // function getUrlParam(name) {
    //     var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    //     var r = window.location.search.substr(1).match(reg);  //匹配目标参数
    //     if (r != null) return unescape(r[2]);
    //     return null; //返回参数值
    // }
    //
    // var code = getUrlParam('code');
    //
    //
    // if (sessionStorage.getItem('openid') == null) {
    //     $.ajax({
    //         url: OPENIDURL,
    //         type: "GET",
    //         async: true,
    //         data: {
    //             code: code
    //         },
    //         dataType: 'jsonp',
    //         success: function (data) {
    //             if (data.code == '000') {
    //                 openid = data.data.openid;
    //                 sessionStorage.setItem('openid', data.data.openid);
    //                 // sessionStorage.setItem('openid', 'weww1');
    //                 orderEv.init();
    //             } else {
    //                 layer.msg(data.msg);
    //             }
    //         },
    //         error: function (data) {
    //         }
    //     });
    // } else {
    //
    //     openid = sessionStorage.getItem('openid');
    //     if (openid != null) {
    //         orderEv.init();
    //     }
    //
    // }
    orderDetailEv.init();
});