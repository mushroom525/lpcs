/**
 * Created by Administrator on 2017/3/30.
 */
var BASEURL = 'http://www.heeyhome.com/lpcs/home/';
var ORDERLISTURL = BASEURL + 'order/orderlist'; // 订单列表
var OPENIDURL = BASEURL + 'index/index'; // openid
var SUGGESTIONURL = BASEURL + 'suggestion/add'; // 意见反馈
var AGAINORDERURL = BASEURL + 'order/orderagain'; // 再来一单
var ORDERCANCELURL = BASEURL + 'order/ordercancel'; // 取消订单
var ORDERPAYURL = BASEURL + 'order/order_continue'; // 付款
var ORDERSTEPURL = BASEURL + 'order/order_step'; // 判断是否支付成功


var path = 'http://www.heeyhome.com/lpcs/';///untitled

var openid = 'o-X7mw822W0t7e9u7gqwkrxsb3-I';//o-X7mw822W0t7e9u7gqwkrxsb3-I

var orderEv = {
    init: function () {
        var self = this;
        self.initDataEvent();
        self.submitSuggestion();
        self.againOrder();
        self.goBackEvent();//意见反馈中的返回
        // self.countdownEvent();
        self.aa();
    },
    /**
     * 返回上一页
     */
    goBackEvent: function () {
        $('.go_back').click(function () {
            window.location.href = path + 'view/orderList.html';
        })
    },
    /**
     * 倒计时
     */
    countdownEvent: function (orderTime, cancelOrderid) {

        var myDate = new Date();
        var dateArr = orderTime.split(' ')[0].split('-');
        var timeArr = orderTime.split(' ')[1].split(':');
        var orderTimeDate = new Date(dateArr[0], dateArr[1] - 1, dateArr[2], timeArr[0], timeArr[1], timeArr[2]);//转换成date类型
        var time_difference = myDate.getTime() - orderTimeDate.getTime();//现在时间毫秒值-下单时间毫秒值
        var intDiff = parseInt(900 - time_difference / 1000); //倒计时总秒数量

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
                    orderEv.cancelOrder(cancelOrderid);
                }
                if (minute <= 9) minute = '0' + minute;
                if (second <= 9) second = '0' + second;
                $('.orderList[order_id=' + cancelOrderid + '] .orderList_right span i').html(minute + ':' + second);
                intDiff--;
            }, 1000);
        }

        $(function () {
            timer(intDiff);
        });
    },
    /**
     * 获取接口信息
     */
    initDataEvent: function () {
        var $content = $('.orderList_wrap');
        $.ajax({
            url: ORDERLISTURL,
            type: "GET",
            async: true,
            data: {
                openid: openid
            },
            dataType: 'jsonp',
            success: function (data) {
                if (data.code == '000') {
                    console.log(data.data);
                    $.each(data.data, function (i, v) {
                        var stitching = '<div order_id="' + v.order_id + '" class="orderList">';
                        stitching += '<div class="orderListWrap">';
                        stitching += '<div class="orderList_left" style="cursor: pointer">';
                        stitching += '<h5><span class="name">' + v.name + '&nbsp;&nbsp;</span><span class="phone">' + v.phone + '</span></h5>';
                        stitching += '<p>下单时间：<span>' + v.order_time + '</span></p>';
                        stitching += '<p>购买数量：<span>' + v.goods_num + '</span></p>';
                        stitching += '<p>消费金额：<span>' + v.total_amount + '</span></p>';
                        stitching += '</div>';
                        stitching += '<div class="orderList_right">';
                        stitching += '<p>' + v.order_step_ch + '</p>';
                        if (v.order_step == '0') {//待支付
                            stitching += '<span>剩余<i></i></span>';
                            stitching += '<a class="order_right cancel_order" href="javascript:;">取消订单</a>';
                            stitching += '<a class="order orderList_pay" href="javascript:;">去付款</a>';
                            orderEv.countdownEvent(v.order_time, v.order_id);
                        } else if (v.order_step == '1') {//待接单
                            stitching += '<a class="order_right" href="javascript:;">联系商家</a>';
                            stitching += '<a class="order again" href="javascript:;">再来一单</a>';
                        } else if (v.order_step == '2') {//待配送
                            stitching += '<a class="order again" href="javascript:;">再来一单</a>';
                        } else if (v.order_step == '3') {//已完成
                            if (v.is_suggest == '0') {//没有反馈过
                                stitching += '<a class="order_right suggestion" href="javascript:;">意见反馈</a>';
                                stitching += '<a class="order again" href="javascript:;">再来一单</a>';
                            } else {//有反馈过
                                stitching += '<a class="order again" href="javascript:;">再来一单</a>';
                            }
                        } else if (v.order_step == '4') {//已取消
                            if (v.is_suggest == '0') {
                                stitching += '<a class="order_right suggestion" href="javascript:;">意见反馈</a>';
                                stitching += '<a class="order again" href="javascript:;">再来一单</a>';
                            } else {
                                stitching += '<a class="order again" href="javascript:;">再来一单</a>';
                            }
                        }
                        stitching += '</div>';
                        stitching += '</div>';
                        stitching += '<div class="del_btn">删除</div>';
                        stitching += '</div>';
                        $content.append(stitching);
                    });
                    orderEv.suggestionAdd();
                    orderEv.orderDetail();
                    orderEv.cancelOrderClick();
                    orderEv.payOrder();
                    orderEv.delOrder();
                    // orderEv.countdownEvent();
                } else {
                    layer.msg(data.msg);
                }
            },
            error: function (data) {
            }
        });
    },
    /**
     * 意见反馈
     */
    suggestionAdd: function () {
        $(document).on('click', '.suggestion', function () {
            var orderid = $(this).parents('.orderList').attr('order_id');
            sessionStorage.setItem('orderid', orderid);
            window.location.href = path + 'view/suggestion.html';
        });
    },
    /**
     * 点击订单列表跳详情页面
     */
    orderDetail: function () {
        $(document).on('click', '.orderList_left', function () {
            var orderDetailId = $(this).parents('.orderList').attr('order_id');
            sessionStorage.setItem('orderDetailId', orderDetailId);
            window.location.href = path + 'view/orderDetail.html';
        });
    },
    /**
     * 再来一单
     */
    againOrder: function () {
        $(document).on('click', '.again', function () {
            var againOrderid = $(this).parents('.orderList').attr('order_id');
            $.ajax({
                url: AGAINORDERURL,
                type: "GET",
                async: true,
                data: {
                    order_id: againOrderid
                },
                dataType: 'jsonp',
                success: function (data) {
                    if (data.code == '000') {
                        window.location.href = path + 'index.html';
                    } else {
                        layer.msg(data.msg);
                    }
                },
                error: function (data) {
                }
            });
        });
    },
    /**
     * 取消订单接口
     */
    cancelOrder: function (orderid) {
        $.ajax({
            url: ORDERCANCELURL,
            type: "GET",
            async: true,
            data: {
                order_id: orderid
            },
            dataType: 'jsonp',
            success: function (data) {
                if (data.code == '000') {
                    location.reload();
                } else {
                    layer.msg(data.msg);
                }
            },
            error: function (data) {
            }
        });
    },
    /**
     * 取消订单按钮点击事件
     */
    cancelOrderClick: function () {
        $(document).on('click', '.cancel_order', function () {
            var cancelOrderid = $(this).parents('.orderList').attr('order_id');
            orderEv.cancelOrder(cancelOrderid);
        });
    },
    /**
     * 去付款
     */
    payOrder: function () {
        $(document).on('click', '.orderList_pay', function () {
            var payOrderid = $(this).parents('.orderList').attr('order_id');
            $.ajax({
                url: ORDERPAYURL,
                type: "GET",
                async: true,
                data: {
                    order_id: payOrderid
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
        });
    },
    /**
     * 提交意见反馈
     */
    submitSuggestion: function () {
        var orderid = sessionStorage.getItem('orderid');
        $(document).on('click', '.suggestion_submit', function () {
            $.ajax({
                url: SUGGESTIONURL,
                type: "GET",
                async: true,
                data: {
                    openid: openid,
                    order_id: orderid,
                    content: $('.suggestion_Wrap textarea').val()
                },
                dataType: 'jsonp',
                success: function (data) {
                    if (data.code == '000') {
                        window.location.href = path + 'view/orderList.html';
                    } else {
                        layer.msg(data.msg);
                    }
                },
                error: function (data) {
                }
            });
        });
    },
    /**
     * 删除订单
     */
    delOrder: function () {
        var block_height = parseInt($('.orderList').outerHeight());
        $('.del_btn').css('line-height', block_height + 'px');
    },
    aa: function () {
        function ad(target) {
            var obj = target && target.parentNode;
            return obj && (obj.className == 'orderList' ? obj : ad(obj));
        }

        var initX;//触摸位置
        var moveX;//滑动时的位置
        var X = 0;//移动距离
        var objX = 0;//目标对象位置
        window.addEventListener('touchstart', function (event) {
            // event.preventDefault();
            // var obj = event.target.parentNode;
            var obj = ad(event.target);
            if (obj) {
                $('.orderList').css('WebkitTransform', 'translateX(0px)');
                // .style.WebkitTransform = "translateX(" + 0 + "px)";
                initX = event.targetTouches[0].pageX;
                objX = (obj.style.WebkitTransform.replace(/translateX\(/g, "").replace(/px\)/g, "")) * 1;
            }
            if (objX == 0) {
                window.addEventListener('touchmove', function (event) {
                    // if(obj.style.WebkitTransform)
                    // event.preventDefault();
                    // var ssss = ad(event.target);
                    // var obj = event.target.parentNode;
                    var obj = ad(event.target);
                    if (obj) {
                        // var objX1 = (obj.style.WebkitTransform.replace(/translateX\(/g, "").replace(/px\)/g, "")) * 1;
                        // if (objX1 < 0) {
                        //     event.preventDefault();
                        // }
                        moveX = event.targetTouches[0].pageX;
                        X = moveX - initX;
                        if (X > 0) {
                            obj.style.WebkitTransform = "translateX(" + 0 + "px)";
                        } else if (X < 0) {
                            var l = Math.abs(X);
                            obj.style.WebkitTransform = "translateX(" + -l + "px)";
                            if (l > 80) {
                                l = 80;
                                obj.style.WebkitTransform = "translateX(" + -l + "px)";
                            }
                        }
                    }
                });
            } else if (objX < 0) {
                window.addEventListener('touchmove', function (event) {
                    event.preventDefault();
                    // var obj = event.target.parentNode;
                    var obj = ad(event.target);
                    if (obj) {
                        moveX = event.targetTouches[0].pageX;
                        X = moveX - initX;
                        if (X > 0) {
                            var r = -80 + Math.abs(X);
                            obj.style.WebkitTransform = "translateX(" + r + "px)";
                            if (r > 0) {
                                r = 0;
                                obj.style.WebkitTransform = "translateX(" + r + "px)";
                            }
                        } else { //向左滑动
                            obj.style.WebkitTransform = "translateX(" + -80 + "px)";
                        }
                    }
                });
            }

        });
        window.addEventListener('touchend', function (event) {
            // event.preventDefault();
            // var obj = event.target.parentNode;
            var obj = ad(event.target);
            if (obj) {
                objX = (obj.style.WebkitTransform.replace(/translateX\(/g, "").replace(/px\)/g, "")) * 1;
                if (objX > -40) {
                    obj.style.WebkitTransform = "translateX(" + 0 + "px)";
                } else {
                    obj.style.WebkitTransform = "translateX(" + -80 + "px)";
                }
            }
        })

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
                window.location.href = path + 'view/orderList.html';
                return;
            } else if (res.err_msg == 'get_brand_wcpay_request:fail') {
                layer.alert("支付失败");
                window.location.href = path + 'view/orderList.html';
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
                            window.location.href = path + 'view/orderDetail.html';
                        }
                    },
                    error: function (data) {
                    }
                });

            } else {
                layer.alert("未知错误" + res.error_msg);
                window.location.href = path + 'view/orderList.html';
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
    orderEv.init();
});