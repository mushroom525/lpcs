/**
 * Created by Administrator on 2017/3/30.
 */
var BASEURL = 'http://www.heeyhome.com/lpcs/home/';
var ORDERLISTURL = BASEURL + 'order/orderlist'; // 订单列表
var OPENIDURL = BASEURL + 'index/index'; // openid
var SUGGESTIONURL = BASEURL + 'suggestion/add'; // 意见反馈
var AGAINORDERURL = BASEURL + 'order/orderagain'; // 再来一单


var path = 'http://www.heeyhome.com/lpcs/';///untitled

var openid = 'o-X7mw822W0t7e9u7gqwkrxsb3-I';

var orderEv = {
    init: function () {
        var self = this;
        self.initDataEvent();
        self.submitSuggestion();
        self.goBackEvent();
        self.againOrder();
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
     * 获取接口信息
     */
    initDataEvent: function () {
        // alert(openid);
        var $content = $('#order_container');
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
                        stitching += '<div class="orderList_left">';
                        stitching += '<h5><span class="name">' + v.name + '&nbsp;&nbsp;</span><span class="phone">' + v.phone + '</span></h5>';
                        stitching += '<p>下单时间：<span>' + v.order_time + '</span></p>';
                        stitching += '<p>购买数量：<span>' + v.goods_num + '</span></p>';
                        stitching += '<p>消费金额：<span>' + v.total_amount + '</span></p>';
                        stitching += '</div>';
                        stitching += '<div class="orderList_right">';
                        stitching += '<p>' + v.order_step_ch + '</p>';
                        if (v.order_step == '0') {//待支付
                            stitching += '<a class="order" href="javascript:;">去付款</a>';
                        } else if (v.order_step == '1') {//待接单
                            stitching += '<a class="order cancel_order" href="javascript:;">取消订单</a>';
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
                        $content.append(stitching);
                    });
                    orderEv.suggestionAdd();
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
            // alert(orderid);
            sessionStorage.setItem('orderid', orderid);
            window.location.href = path + 'view/suggestion.html';
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
                    alert(1);
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
                    alert(1);
                }
            });
        });
    }
};

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
    //                 alert(data.msg);
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