/**
 * Created by Administrator on 2017/3/30.
 */
var BASEURL = 'http://www.heeyhome.com/lpcs/home/';
var ORDERLISTURL = BASEURL + 'seller/orderlist'; // 商家订单列表


var path = 'http://www.heeyhome.com/lpcs/';///untitled

var openid = 'o-X7mw822W0t7e9u7gqwkrxsb3-I';//o-X7mw822W0t7e9u7gqwkrxsb3-I

var merorderEv = {
    init: function () {
        var self = this;
        self.initDataEvent();
        // self.swiperDel();
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
                        if (v.order_step == '1') {//待接单
                            stitching += '<a class="order" href="javascript:;">确认接单</a>';
                        } else if (v.order_step == '2') {//待配送
                            stitching += '<a class="order" href="javascript:;">发布配送</a>';
                        }
                        stitching += '</div>';
                        stitching += '</div>';
                        stitching += '<div class="del_btn">删除</div>';
                        stitching += '</div>';
                        $content.append(stitching);
                    });
                    merorderEv.orderDetail();
                    // orderEv.delOrder();
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
     * 点击订单列表跳详情页面
     */
    orderDetail: function () {
        $(document).on('click', '.orderList_left', function () {
            var merorderDetailId = $(this).parents('.orderList').attr('order_id');
            sessionStorage.setItem('merorderDetailId', merorderDetailId);
            window.location.href = path + 'view/merOrderDetail.html';
        });
    },
    /**
     * 删除订单
     */
    delOrder: function () {
        var block_height = parseInt($('.orderList').outerHeight());
        var $delBtn = $('.del_btn');
        $delBtn.css('line-height', block_height + 'px');
        $delBtn.click(function () {
            var orderid = $(this).parent().attr('order_id');
            $.ajax({
                url: ORDERDELURL,
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
        })
    },
    /**
     * 左滑出现删除按钮
     */
    swiperDel: function () {
        function ad(target) {
            var obj = target && target.parentNode;
            return obj && (obj.className == 'orderList' ? obj : ad(obj));
        }

        var initX; //触摸位置X
        var initY; //触摸位置Y
        var moveX; //滑动时的位置X
        var moveY; //滑动时的位置Y
        var X = 0; //移动距离X
        var Y = 0; //移动距离Y
        var flagX = 0; //是否是左右滑动 0为初始，1为左右，2为上下，在move中设置，在end中归零
        var objX = 0; //目标对象位置

        window.addEventListener('touchstart', function (event) {
            // event.preventDefault();
            // var obj = event.target.parentNode;
            var obj = ad(event.target);
            if (obj) {
                // $('.orderList').css('WebkitTransform', 'translateX(0px)');
                // .style.WebkitTransform = "translateX(" + 0 + "px)";
                initX = event.targetTouches[0].pageX;
                initY = event.targetTouches[0].pageY;
                objX = (obj.style.WebkitTransform.replace(/translateX\(/g, "").replace(/px\)/g, "")) * 1;
            }
            if (objX == 0) {
                window.addEventListener('touchmove', function (event) {
                    // 判断滑动方向，X轴阻止默认事件，Y轴跳出使用浏览器默认
                    if (flagX == 0) {
                        setScrollX(event);
                        return;
                    } else if (flagX == 1) {
                        event.preventDefault();
                    } else {
                        return;
                    }
                    var obj = ad(event.target);
                    if (obj) {
                        moveX = event.targetTouches[0].pageX;
                        X = moveX - initX;
                        if (X > 0) {
                            obj.style.WebkitTransform = "translateX(" + 0 + "px)";
                        } else if (X < 0) {
                            var l = Math.abs(X);
                            obj.style.WebkitTransform = "translateX(" + -l + "px)";
                            if (l > 60) {
                                l = 60;
                                obj.style.WebkitTransform = "translateX(" + -l + "px)";
                            }
                        }
                    }
                });
            } else if (objX < 0) {
                window.addEventListener('touchmove', function (event) {
                    // 判断滑动方向，X轴阻止默认事件，Y轴跳出使用浏览器默认
                    if (flagX == 0) {
                        setScrollX(event);
                        return;
                    } else if (flagX == 1) {
                        event.preventDefault();
                    } else {
                        return;
                    }
                    // var obj = event.target.parentNode;
                    var obj = ad(event.target);
                    if (obj) {
                        moveX = event.targetTouches[0].pageX;
                        X = moveX - initX;
                        if (X > 0) {
                            var r = -60 + Math.abs(X);
                            obj.style.WebkitTransform = "translateX(" + r + "px)";
                            if (r > 0) {
                                r = 0;
                                obj.style.WebkitTransform = "translateX(" + r + "px)";
                            }
                        } else { //向左滑动
                            obj.style.WebkitTransform = "translateX(" + -60 + "px)";
                        }
                    }
                });
            }
        });
        window.addEventListener('touchend', function (event) {
            var obj = ad(event.target);
            if (obj) {
                objX = (obj.style.WebkitTransform.replace(/translateX\(/g, "").replace(/px\)/g, "")) * 1;
                if (objX > -30) {
                    obj.style.WebkitTransform = "translateX(" + 0 + "px)";
                } else {
                    obj.style.WebkitTransform = "translateX(" + -60 + "px)";
                }
            }
            flagX = 0;
        });
        //设置滑动方向
        function setScrollX(event) {
            moveX = event.targetTouches[0].pageX;
            moveY = event.targetTouches[0].pageY;
            X = moveX - initX;
            Y = moveY - initY;

            if (Math.abs(X) > Math.abs(Y)) {
                flagX = 1;
            } else {
                flagX = 2;
            }
            return flagX;
        }
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
    merorderEv.init();
});