/**
 * Created by Administrator on 2017/3/27.
 */
/**
 * Created by Administrator on 2017/3/9.
 */

var BASEURL = 'http://www.heeyhome.com/lpcs/home/';
var CATELISTURL = BASEURL + 'category/catelist'; // 分类列表
var TODAYREURL = BASEURL + 'goodslist/todayrecommend'; // 今日推荐列表
var GOODLISTURL = BASEURL + 'goodslist/goodslist'; // 商品列表
var OPENIDURL = BASEURL + 'index/index'; // openid
var GWCINFOURL = BASEURL + 'cart/index'; // 获取默认购物车内容
var ADDCARTURL = BASEURL + 'cart/add'; // 加入购物车
var DELCARTURL = BASEURL + 'cart/del'; // 删除购物车
var EMPTYCARTURL = BASEURL + 'cart/emptycart'; // 清空购物车

var path = 'http://www.heeyhome.com/lpcs/view/';///untitled
var openid = '';

var freshIndex = {
    init: function () {
        var self = this;
        self.initInfoEvent();
        self.bgHeightEvent();
        self.getCateListEvent();
        self.getTodayRecommend();
        self.leftClickEvent();
        self.gwcEvent();
        self.gwcContentInit();
        // self.gwcNumEvent();
        self.closeWrapEvent();
        self.titleScrollEvent();
        self.recommendClickEvent();
        //self.getUrlEvent();
        self.goToPay();
        self.addCartEvent();
        self.delCartEvent();
        self.emptyCartEvent();
        self.changeGwcEvent();
    },

    /**
     * 初始化
     */
    initInfoEvent: function () {
        var $icon = $('.icon');
        if ($icon.html() == '0') {
            $icon.hide();
        } else {
            $icon.show();
        }
    },
    /**
     * 点击li添加样式
     */
    leftClickEvent: function () {
        $(document).on('click', '.left_slide li', function () {
            $('.left_slide li').removeClass('active');
            $(this).addClass('active');
            freshIndex.getDescribeEvent();
        })
    },
    /**
     * 点击今日推荐
     */
    recommendClickEvent: function () {
        $('#today_recommend').click(function () {
            freshIndex.getTodayRecommend();
        })
    },
    /**
     * 动态获取宽高度
     */
    bgHeightEvent: function () {
        var block_height = parseInt($('.title').height());
        var block_width = parseInt($('.left_img img').width());
        $('.title').css('line-height', block_height + 'px');
        $('.left_img').css('height', block_width + 'px');
    },
    /**
     * tab滑动
     */
    titleScrollEvent: function () {
        var self = this;
        $(document).ready(function () {
            var mySwiper = new Swiper('.swiper-container', {
                slidesPerView: 4,//'auto'
                observer: true,//修改swiper自己或子元素时，自动初始化swiper
                observeParents: true//修改swiper的父元素时，自动初始化swiper
            });
            $('.swiper-slide').eq(0).addClass('swiper_active');
        });

        $(document).on('click', '.swiper-slide', function () {
            $(this).addClass('swiper_active').siblings().removeClass('swiper_active');
        });

        $(document).on('click', '.other_cate', function () {
            var cate_id = $(this).attr('cate_id');
            self.getGoodsList(cate_id);
            self.initFloorNavEvent();
        })
    },
    /**
     * 获取细节内容
     */
    getDescribeEvent: function () {
        var $active = $('.left_slide li[class="active"]').find('a');
        var $a = $active.html();
        var $tab = $active.attr('tab');
        var $len = $('#' + $tab + ' li').length;
        $('.detail_describe').html($a + '（' + $len + '）');
    },
    /**
     * 点击购物车出现购物车详情内容
     */
    gwcEvent: function () {
        var starty;
        //手指接触屏幕
        var abcFlag = false;
        var top = 0;
        var interval;
        $('.gwc_bottom').click(function () {
            var $li = $('.gwcDetail_content li');
            if ($li.length != '0') {
                if ($('#gec_detail').is(':hidden')) {
                    $('#gec_detail').show();
                    $('#wrap').show();
                    // $('.detail_wrap').css('position', 'fixed');
                    var u = navigator.userAgent;
                    var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
                    if (isAndroid) {
                        show_popwindow();
                    }
                    function show_popwindow() {
                        //页面加载时，弹出框是隐藏的，当点击弹出按钮时，弹出框弹出
                        document.getElementById("wrap").style.display = "block";
                        //下面的两句是为了防止底部页面滑动。注：必须对html和body都设置overflow:hidden，移动端才能禁止滑动
                        document.documentElement.style.overflow = 'hidden';
                        document.body.style.overflow = 'hidden';
                    }

                    document.getElementById('abc').addEventListener("touchstart", function (e) {
                        window.event.returnValue = false;
                    }, false);

                    // document.addEventListener('touchmove', function (event) {
                    //     //判断条件,条件成立才阻止背景页面滚动,其他情况不会再影响到页面滚动
                    //     if (!$(".wrap").is(":hidden")) {
                    //         event.preventDefault();
                    //
                    //     }
                    // }, {passive: false});
                    document.getElementById("wrap").ontouchstart = function (e) {
                        e.preventDefault();
                    };
                    var body_width = parseInt($(window).width());
                    var body_height = parseInt($(window).height());
                    $('#wrap').css({'width': body_width, 'height': body_height});
                } else {
                    $('#gec_detail').hide();
                    $('#wrap').hide();
                }
            }
        });
    },
    /**
     * 触摸遮罩层关闭遮罩层与展开的内容
     */
    closeWrapEvent: function () {
        document.getElementById("wrap").ontouchstart = function (e) {
            $('#wrap').hide();
            $('#gec_detail').hide();
        };
    },
    /**
     * 楼层导航事件
     */
    initFloorNavEvent: function () {

        $(document).on('click', '.left_slide li', function (e) {
            var items = $('.content_li');
            var x = $(this).index();
            var divTop = items.eq(x).offset().top;
            freshIndex.getDescribeEvent();
            e.stopPropagation();

            $("html,body").stop().animate({
                scrollTop: divTop
            }, 10);
        });
        document.addEventListener('touchmove', function () {
            var items = $('.content_li');
            var scrollTop = $(document).scrollTop();
            var oTabUl = $('.left_slide');
            var curId = '';
            freshIndex.getDescribeEvent();

            items.each(function () {
                var m = $(this); //定义变量，获取当前类
                var itemsTop = m.offset().top; //定义变量，获取当前类的top偏移量
                if (scrollTop > itemsTop - 100) {
                    curId = m.attr("id");
                } else {
                    return false;
                }
            });

            //给相应的楼层设置cur,取消其他楼层的cur
            var curLink = oTabUl.find("a");
            if (curId && curLink.attr("tab") != curId) {
                curLink.parent().removeClass("active");
                oTabUl.find("[tab= '" + curId + "']").parent().addClass("active");
            }
        });
    },
    /**
     * 加入购物车飞入事件
     */
    gotoGwcEvent: function (event, plus) {
        var offset = $(".gwc").offset();
        var img = plus.parents('li').find('.left_img img').attr('src');
        var flyer = $('<img class="u-flyer" src="' + img + '" style="width:40px;height: 40px;">');
        flyer.fly({
            start: {
                left: event.pageX,
                top: event.pageY - $(document).scrollTop()
            },
            end: {
                left: offset.left + 10,
                top: offset.top + 10,
                width: 0,
                height: 0
            },
            onEnd: function () {
                // var gwc_num = $('.icon').text();
                // gwc_num++;
                // $('.icon').text(gwc_num);
                // this.destory();
            }
        });
    },
    /**
     * 购物车内容拼接
     */
    gwcContentEvent: function (cartData) {
        var $content = $('.gwcDetail_content ul');
        $content.empty();
        $.each(cartData, function (i, v) {
            var stitching = '<li data-id="' + v.goods_id + '"> <span class="name">' + v.goods_name + '</span>';
            stitching += '<span class="money">￥<i class="money_i">' + v.discount_price + '</i>/<i>' + v.unit + '</i></span>';
            stitching += '<span class="operate">';
            stitching += '<a class="reduce" href="javascript:;">';
            stitching += '<img src="img/reduce.png">';
            stitching += '</a>';
            stitching += '<span class="num">' + v.goods_num + '</span>';
            stitching += '<a class="plus" href="javascript:;">';
            stitching += '<img src="img/plus.png">';
            stitching += '</a>';
            stitching += '</span>';
            stitching += '</li>';
            $content.append(stitching);
        });

    },
    /**
     * 购物车内容初始化
     */
    gwcContentInit: function () {
        var $content = $('.gwcDetail_content ul');
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
                        var stitching = '<li data-id="' + v.goods_id + '"> <span class="name">' + v.goods_name + '</span>';
                        stitching += '<span class="money">￥<i class="money_i">' + v.discount_price + '</i>/<i>' + v.unit + '</i></span>';
                        stitching += '<span class="operate">';
                        stitching += '<a class="reduce" href="javascript:;">';
                        stitching += '<img src="img/reduce.png">';
                        stitching += '</a>';
                        stitching += '<span class="num">' + v.goods_num + '</span>';
                        stitching += '<a class="plus" href="javascript:;">';
                        stitching += '<img src="img/plus.png">';
                        stitching += '</a>';
                        stitching += '</span>';
                        stitching += '</li>';
                        $content.append(stitching);
                    });
                }
            },
            error: function (data) {
            }
        });
    },

    /**
     * 购物车变化
     */
    changeGwcEvent: function () {
        // var $li = $('.gwcDetail_content li');
        // if ($li.length == 0) {
        //     $('.icon').hide();//隐藏数字
        //     $('#gwc i').removeClass('active_i');//购物车变暗
        //     $('#gwc span').removeClass('active_span');//文字
        //     $('#gwc b').html('购物车是空的');//文字
        //     $('#gwc span em').hide();//￥
        // } else {
        //     $('.icon').show();//显示数字
        //     $('#gwc i').addClass('active_i');//购物车变亮
        //     $('#gwc span').addClass('active_span');//文字
        //     $('#gwc span em').show();//￥
        // }
        // var $sum = 0;
        // for (var i = 0; i < $li.length; i++) {//总数变化
        //     var $money = $('.gwcDetail_content .money_i').eq(i).html();
        //     var $num = $('.gwcDetail_content .num').eq(i).html();
        //     $sum += parseFloat($money * $num);
        //     $('.gwc span b').html($sum.toFixed(2));
        // }
        // freshIndex.changePayEvent($sum);
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
                    if (data.data.status.amount > 0) {
                        $('.icon').show();//显示数字
                        $('.icon').html(data.data.status.quantity);//显示数字
                        $('#gwc i').addClass('active_i');//购物车变亮
                        $('#gwc span').addClass('active_span');//文字
                        $('#gwc span em').show();//￥
                        $('#gwc span b').html(data.data.status.amount);//￥
                        freshIndex.changePayEvent(data.data.status.amount);
                    }
                } else {
                    $('.icon').hide();//隐藏数字
                    $('#gwc i').removeClass('active_i');//购物车变暗
                    $('#gwc span').removeClass('active_span');//文字
                    $('#gwc b').html('购物车是空的');//文字
                    $('#gwc span em').hide();//￥
                    $('.gwc_submit a').html('还差￥<i>30</i>元起送');//￥
                }
            },
            error: function (data) {
            }
        });
    },
    /**
     * 加入购物车
     */
    addCartEvent: function () {
        $(document).on('click', '.plus', function () {
            var goods_id = $(this).parents('li').attr('data-id');
            var cate_id = $(this).attr('cate_id');
            freshIndex.gotoGwcEvent(event, $(this));
            $.ajax({
                url: ADDCARTURL,
                type: "GET",
                async: true,
                data: {
                    openid: openid,
                    goods_id: goods_id
                },
                dataType: 'jsonp',
                success: function (data) {
                    console.log(data.data);
                    var cartData = data.data.carts;
                    freshIndex.gwcContentEvent(cartData);
                    if (cate_id != undefined && cate_id != '' && cate_id != null) {
                        freshIndex.getGoodsList(cate_id);
                    } else {
                        freshIndex.getTodayRecommend();
                    }
                    freshIndex.changeGwcEvent();
                },
                error: function (data) {
                }
            });
        });
    },
    /**
     * 删除购物车
     */
    delCartEvent: function () {
        $(document).on('click', '.reduce', function () {
            var goods_id = $(this).parents('li').attr('data-id');
            var cate_id = $(this).attr('cate_id');
            console.log(goods_id);
            $.ajax({
                url: DELCARTURL,
                type: "GET",
                async: true,
                data: {
                    openid: openid,
                    goods_id: goods_id
                },
                dataType: 'jsonp',
                success: function (data) {
                    console.log(data.data);
                    var cartData = data.data.carts;
                    freshIndex.gwcContentEvent(cartData);
                    if (cate_id != undefined && cate_id != '' && cate_id != null) {
                        freshIndex.getGoodsList(cate_id);
                    } else {
                        freshIndex.getTodayRecommend();
                    }
                    freshIndex.changeGwcEvent();
                    var $li = $('.gwcDetail_content li');
                    if ($li.length == '0') {
                        $('#gec_detail').hide();
                        $('#wrap').hide();
                    }
                },
                error: function (data) {
                }
            });
        });
    },
    /**
     * 清空购物车
     */
    emptyCartEvent: function () {
        $('.top_right').click(function () {
            layer.open({
                content: '清空购物车？',
                btn: ['清空', '取消'],
                yes: function (index) {
                    $.ajax({
                        url: EMPTYCARTURL,
                        type: "GET",
                        async: true,
                        data: {
                            openid: openid
                        },
                        dataType: 'jsonp',
                        success: function (data) {
                            console.log(data.data);
                            location.reload();
                        },
                        error: function (data) {
                        }
                    });
                    layer.close(index);
                }
            });
        });
    },
    /**
     * 结算按钮变化
     */
    changePayEvent: function ($sum) {
        var $submita = $('.gwc_submit a');
        if ($sum >= 30) {
            $submita.html('去结算');
            $submita.addClass('active_a');
        } else {
            $submita.html('还差￥<i>30</i>元起送');
            $submita.find('i').html((30 - $sum).toFixed(2));//计算保留2位小数!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            $submita.removeClass('active_a');
        }
    },
    /**
     * 去结算
     */
    goToPay: function () {
        $(document).on('click', '.gwc_submit .active_a', function () {
            /**
             *
             * 获取当前时间
             */
            function p(s) {
                return s < 10 ? '0' + s : s;
            }

            var myDate = new Date();

            //获取当前日
            var h = myDate.getHours();       //获取当前小时数(0-23)
            var m = myDate.getMinutes();     //获取当前分钟数(0-59)

            var now = p(h) + ':' + p(m);
            sessionStorage.setItem('time', now);
            $('#wrap').hide();
            $('#gec_detail').hide();
            window.location.href = path + 'order.html';
        })
    },
    /**
     * 获取分类列表
     */
    getCateListEvent: function () {
        $.ajax({
            url: CATELISTURL,
            type: "GET",
            async: true,
            dataType: 'jsonp',
            success: function (data) {
                console.log(data.data);
                var $swiper = $('.swiper-wrapper');
                $.each(data.data, function (i, v) {
                    var cateContent = '<div class="swiper-slide other_cate" cate_id="' + v.cate_id + '">';
                    cateContent += '' + v.cate_name + '</div>';
                    $swiper.append(cateContent);
                });
            },
            error: function (data) {
            }
        });
    },
    /**
     * 获取今日推荐列表
     */
    getTodayRecommend: function () {
        $.ajax({
            url: TODAYREURL,
            type: "GET",
            async: true,
            data: {
                openid: openid
            },
            dataType: 'jsonp',
            success: function (data) {
                console.log(data.data);
                $('.detail_describe').html('今日推荐（' + data.data.length + '）');
                var $leftSlide = $('.left_slide ul');
                var $detailWrap = $('#detail_wrap');
                $leftSlide.empty();
                $detailWrap.empty();
                $leftSlide.html('<li class="active"><a href="javascript:;">今日推荐</a></li>');
                $.each(data.data, function (i, v) {
                    var todayContent = '<div class="content_li">';
                    todayContent += '<ul>';
                    todayContent += '<li data-id="' + v.goods_id + '">';
                    todayContent += '<div class="left_img">';
                    todayContent += '<img src="http://www.heeyhome.com/lpcs/' + v.goods_img + '">';
                    todayContent += '</div>';
                    todayContent += '<div class="left_describe">';
                    todayContent += '<header>' + v.goods_name + '</header>';
                    todayContent += '<p>月销<i>' + (v.sales || 0) + '</i>笔</p>';
                    todayContent += '<strong>￥<i>' + v.price + '</i>/<b>' + v.unit + '</b></strong>';
                    todayContent += '</div>';
                    todayContent += '<div class="right_add">';
                    if (v.goods_num > 0) {
                        todayContent += '<a class="reduce fl" href="javascript:;">';
                        todayContent += '<img src="img/reduce.png">';
                        todayContent += '</a>';
                        todayContent += '<span class="num fl">' + v.goods_num + '</span>';
                    }
                    todayContent += '<a class="plus fr" href="javascript:;">';
                    todayContent += '<img src="img/plus.png">';
                    todayContent += '</a></div></li></ul></div>';
                    $detailWrap.append(todayContent);
                });
            },
            error: function (data) {
            }
        });
    },
    /**
     * 获取商品列表
     */
    getGoodsList: function (cate_id) {
        $.ajax({
            url: GOODLISTURL,
            type: "GET",
            async: true,
            data: {
                cate_id: cate_id,
                openid: openid
            },
            dataType: 'jsonp',
            success: function (data) {
                console.log(data.data);
                var $leftSlide = $('.left_slide ul');
                var $detailWrap = $('#detail_wrap');
                $leftSlide.empty();
                $detailWrap.empty();
                $.each(data.data, function (m, n) {
                    var leftContent = '<li><a href="javascript:;" tab="' + n.cate_id + '">' + n.cate_name + '</a>';
                    leftContent += '</li>';
                    $leftSlide.append(leftContent);
                    var goodsContent = '<div class="content_li" id="' + n.cate_id + '"><ul>';
                    $.each(n.goods, function (i, v) {
                        goodsContent += '<li data-id="' + v.goods_id + '">';
                        goodsContent += '<div class="left_img">';
                        goodsContent += '<img src="http://www.heeyhome.com/lpcs/' + v.goods_img + '">';
                        goodsContent += '</div>';
                        goodsContent += '<div class="left_describe">';
                        goodsContent += '<header>' + v.goods_name + '</header>';
                        goodsContent += '<p>月销<i>' + (v.sales || 0) + '</i>笔</p>';
                        if (v.tag == '2') {
                            goodsContent += '<strong>￥<i>' + v.discount_price + '</i>/<b>' + v.unit + '</b></strong>';
                            goodsContent += '<strong class="line_through">￥<i>' + v.price + '</i>/<b>' + v.unit + '</b></strong>';
                        } else {
                            goodsContent += '<strong>￥<i>' + v.price + '</i>/<b>' + v.unit + '</b></strong>';
                        }

                        goodsContent += '</div>';
                        goodsContent += '<div class="right_add">';
                        if (v.goods_num > 0) {
                            goodsContent += '<a class="reduce fl" href="javascript:;" cate_id="' + cate_id + '">';
                            goodsContent += '<img src="img/reduce.png">';
                            goodsContent += '</a>';
                            goodsContent += '<span class="num fl">' + v.goods_num + '</span>';
                        }
                        goodsContent += '<a class="plus fr" href="javascript:;" cate_id="' + cate_id + '">';
                        goodsContent += '<img src="img/plus.png">';
                        goodsContent += '</a></div></li>';
                    });
                    goodsContent += '</ul></div>';
                    $detailWrap.append(goodsContent);
                });
                $('.left_slide li').eq(0).addClass('active');
                freshIndex.getDescribeEvent();
            },
            error: function (data) {
            }
        });
    }
};

$(function () {
    function getUrlParam(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
        var r = window.location.search.substr(1).match(reg);  //匹配目标参数
        if (r != null) return unescape(r[2]);
        return null; //返回参数值
    }

    var code = getUrlParam('code');


    if (sessionStorage.getItem('openid') == null) {
        $.ajax({
            url: OPENIDURL,
            type: "GET",
            async: true,
            data: {
                code: code
            },
            dataType: 'jsonp',
            success: function (data) {
                if (data.code == '000') {
                    openid = data.data.openid;
                    sessionStorage.setItem('openid', data.data.openid);
                    // sessionStorage.setItem('openid', 'weww1');
                    freshIndex.init();
                } else {
                    alert(data.msg);
                }
            },
            error: function (data) {
            }
        });
    } else {

        openid = sessionStorage.getItem('openid');
        if (openid != null) {
            freshIndex.init();
        }

    }
});