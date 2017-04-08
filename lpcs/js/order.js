/**
 * Created by Administrator on 2017/3/30.
 */
var BASEURL = 'http://www.heeyhome.com/lpcs/home/';
var ADDRESSLISTURL = BASEURL + 'address/index'; // 地址列表
var GWCINFOURL = BASEURL + 'cart/index'; // 获取默认购物车内容
var CONFIRMPAYURL = BASEURL + 'order/orderproduce'; // 确认付款


var path = 'http://www.heeyhome.com/lpcs/view/';///untitled

var openid = sessionStorage.getItem('openid');//o-X7mw822W0t7e9u7gqwkrxsb3-I

var addressEv = {
    init: function () {
        var self = this;
        self.initDataEvent();
        self.goBackEvent();
        self.chooseAddressEvent();
        self.saveAddressEvent();
        self.gwcContentInit();
        self.selectTimeEvent(); //选择送达时间
        self.clickSelectTimeEvent(); //选择送达时间
        self.confirmPay(); //确认付款
    },
    /**
     * 数据初始化
     */
    initDataEvent: function () {
        var self = this;

        var time = sessionStorage.getItem('time');

        /**
         *startTime: '08:15'
         *endTime:'20:15'
         *minutes:15 间隔
         */
        function getHourAndMinutesArr(startTime, endTime, minutes) {
            var startArr = startTime.split(":");
            var endArr = endTime.split(":");
            var date = new Date();
            var year = date.getFullYear();
            var month = (date.getMonth() + 1) < 10 ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
            var day = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
            var startDate = new Date(year, month, day, startArr[0], startArr[1], 00);
            var endDate = new Date(year, month, day, endArr[0], endArr[1], 00);
            var timeArr = [];
            timeArr.push(startTime);
            while (true) {
                startDate.setMinutes(startDate.getMinutes() + parseInt(minutes));
                var hour = startDate.getHours() < 10 ? '0' + startDate.getHours() : startDate.getHours();
                var minute = startDate.getMinutes() < 10 ? '0' + startDate.getMinutes() : startDate.getMinutes();
                if (startDate.getTime() <= endDate.getTime()) {
                    timeArr.push(hour + ":" + minute);
                } else {
                    break;
                }
            }
            return timeArr;
        }

        // getHourAndMinutesArr('08:15', '20:15', 15);
        //测试数据

        function today() {
            var timeArr1 = time.split(':');
            var arr = ['尽快送达 | 预计' + (parseInt(timeArr1[0]) + 1) + ':' + timeArr1[1]];
            timeArr1[1] = timeArr1[1] - timeArr1[1] % 15 + 15;
            var timeTempArr = getHourAndMinutesArr((parseInt(timeArr1[0]) + 1) + ':' + timeArr1[1], '20:15', 15);
            arr = arr.concat(timeTempArr);
            return arr;
        }

        var timeObj = {
            "今日": today(),
            "明日": getHourAndMinutesArr('08:15', '20:15', 15)
        };
        // console.log(timeObj);

        $("#Jleft_slide ul").html(self.spliceDataInfoEvent(timeObj));
        $("#Jleft_slide ul li").data('time', timeObj);
        $("#J_scroll_holder ul ").html(self.spliceTimeInfoEvent(timeObj, "今日"));
        $("#J_scroll_holder ul li").data('j', timeObj);
        self.selectHiddenEvent(timeObj, "今日", 0)

    },
    /**
     * 返回上一页
     */
    goBackEvent: function () {
        $('.go_back').click(function () {
            window.location.href = 'http://www.heeyhome.com/lpcs/index.html';
        })
    },
    /**
     * 选择送餐地址
     */
    chooseAddressEvent: function () {
        $('.order_address').click(function () {
            sessionStorage.setItem('addressId', $(this).attr("data-id"));
            window.location.href = path + 'address.html';
        })
    },
    /**
     * 购物车内容初始化
     */
    gwcContentInit: function () {
        var $content = $('.shop_content ul');
        $content.empty();
        $.ajax({
                url: GWCINFOURL,
                type: "GET",
                async: true,
                data: {
                    openid: openid
                },
                dataType: 'jsonp',
                success: function (data) {
                    console.log(data.data);
                    if (data.data != '') {
                        $.each(data.data.carts, function (i, v) {
                            var stitching = '<li>';
                            stitching += '<span class="v_name">' + v.goods_name + '</span>';
                            stitching += '<span class="v_num">' + v.goods_num + '</span>';
                            stitching += '<span class="v_price">￥' + v.discount_price + '</span>';
                            stitching += '</li>';
                            $content.append(stitching);
                        });
                        var amount = parseFloat(data.data.status.amount) + parseFloat($('#psf').html());
                        $('.order_money span').html('￥' + amount);
                    }
                },
                error: function (data) {
                }
            });
    },
    /**
     * 保存地址数据
     */
    saveAddressEvent: function () {
        var addressObj = sessionStorage.getItem('addressObj');
        sessionStorage.setItem('addressObj', '');//把选中的地址存到session里
        console.log(addressObj);
        if (addressObj) {
            var addressInfo = JSON.parse(addressObj);
            console.log(addressInfo);
            $('.address_name').html(addressInfo.name);
            $('.address_tel').html(addressInfo.tel);
            $('.address_room').html(addressInfo.room);
            $('.order_address').attr('data-id', addressInfo.id);

        } else {
            /* 获取默认地址数据 */
            $.ajax({
                url: ADDRESSLISTURL,
                type: "GET",
                async: true,
                data: {
                    openid: openid
                },
                dataType: 'jsonp',
                success: function (data) {
                    console.log(data);
                    if (data.code == '000') {
                        $.each(data.data, function (i, v) {
                            if (v.is_default == '1') {
                                $('.order_address').attr('data-id', v.address_id);
                                $('.address_name').html(v.name);
                                $('.address_tel').html(v.phone);
                                $('.address_room').html(v.area + '' + v.address + '' + v.room);
                            } else {
                                $('.order_address').attr('data-id', data.data[0].address_id);
                                $('.address_name').html(data.data[0].name);
                                $('.address_tel').html(data.data[0].phone);
                                $('.address_room').html(data.data[0].area + '' + data.data[0].address + '' + data.data[0].room);
                            }
                        })
                    } else {
                        $('.address').html('请选择一个收货地址');
                        $('.address').css('margin-top', '4%');
                    }
                },
                error: function (data) {
                }
            });
        }
    },
    /**
     * 点击送达时间
     */
    selectTimeEvent: function () {
        $(document).on("touchstart", ".Jsongda", function () {
//			$("#J_scroll_holder ul").attr("data-time", JSON.stringify(timeObj));
            $("#wrap").removeClass("display");
            $("#Jcontainer").slideDown(300);
            $('#time').css('z-index', '1201');
        });
        $(document).on("touchstart", "#wrap", function () {
            $("#Jcontainer").slideUp(300, function () {
                $("#wrap").addClass("display");
                $('#time').css('z-index', '1030');
            });

        });
    },
    /**
     * 选择送达时间
     */
    clickSelectTimeEvent: function () {
        var self = this;
        $(document).on("click", "#J_scroll_holder ul li", function () {
            $(this).find("i").addClass("click");
            $(this).siblings("li").find("i").removeClass("click");
            self.selectHiddenEvent($(this).data("j"), $(this).data("i"), $(this).index());
//			$(".Jsongda").find("span").text($(this).find("p").text());
            $("#Jcontainer").slideUp(300, function () {
                $("#wrap").addClass("display");
                $('#time').css('z-index', '1030');
            });

        });

        $(document).on("click", "#Jleft_slide ul li", function () {
            $(this).addClass("active").siblings("li").removeClass("active");
            $("#J_scroll_holder ul ").html(self.spliceTimeInfoEvent($(this).data("time"), $(this).data("day")));
            $("#J_scroll_holder ul li").data("j", $(this).data("time"));
            self.selectHiddenEvent($(this).data("time"), $(this).data("day"), 0)
        });
    },
    /**
     * 送达日期
     * @param {Object} Obj 对象
     */
    spliceDataInfoEvent: function (Obj) {
        var vrStr = '';
        $.each(Obj, function (i, v) {
            vrStr += '<li class="' + (i == '今日' ? "active" : "") + '" data-day = "' + i + '" data-time = ' + JSON.stringify(Obj) + '>' + i + '</li>';
        });
        return vrStr;
    },
    /**
     * 送达时间
     * @param {Object} Obj 对象
     */
    spliceTimeInfoEvent: function (Obj, name) {
        var vrStr = '';
        $.each(Obj[name], function (i, v) {
            vrStr += '<li data-i="' + name + '"><p>' + v + '</p><i class="' + (i == 0 ? "click" : "") + '"></i></li>';
        });
        return vrStr;
    },
    /**
     * 隐藏域设置
     */
    selectHiddenEvent: function (Obj, name, i) {
        var hiddenObj = {
            dayName: name,
            time: Obj[name][i]
        };
        $("#JselectIt").val(JSON.stringify(hiddenObj));
        var obj = JSON.parse($("#JselectIt").val());
        console.log(obj);
        if (obj.time) {
            if (obj.time.indexOf("尽快送达") >= 0) {
                $(".Jsongda").find("span").text(obj.time);
            } else {
                $(".Jsongda").find("span").text(obj.dayName + obj.time);
            }
        }
    },

    /**
     * 确认付款
     */
    confirmPay: function () {
        $('.order_pay a').click(function () {
            if ($('.order_address').attr('data-id')) {
                $.ajax({
                    url: CONFIRMPAYURL,
                    type: "GET",
                    async: true,
                    data: {
                        openid: openid,
                        address_id: $('.order_address').attr('data-id'),
                        appointment_time: $('.Jsongda span').html(),
                        distribution_cost: $('#psf').html(),
                        remark: $('.order_mark textarea').val()
                    },
                    dataType: 'jsonp',
                    success: function (data) {
                        if (data.code == '000') {
                            var parsedata = JSON.parse(data.data.jsApiParameters);
                            callpay(parsedata);
                        } else {
                            layer.msg(data.msg);
                        }
                    },
                    error: function (data) {
                    }
                });
            } else {
                layer.msg('请添加地址');
            }

        });

    }

};

/**
 * 微信支付
 */
function callpay(jsStr) {
    if (typeof WeixinJSBridge == "undefined") {
        if (document.addEventListener) {
            document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
        } else if (document.attachEvent) {
            document.attachEvent('WeixinJSBridgeReady', jsApiCall);
            document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
        }
    } else {
        jsApiCall(jsStr);
    }
}


//调用微信JS api 支付
function jsApiCall(jsStr) {

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
                layer.msg("您已取消了此次支付");
                return;
            } else if (res.err_msg == 'get_brand_wcpay_request:fail') {
                layer.msg("支付失败");
                return;
            } else if (res.err_msg == 'get_brand_wcpay_request:ok') {
                alert("支付成功！");//跳转到订单页面

            } else {
                layer.msg("未知错误" + res.error_msg);
                return;
            }
        }
    );
}

$(function () {
    addressEv.init();
});