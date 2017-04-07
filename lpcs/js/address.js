/**
 * Created by Administrator on 2017/3/30.
 */
var BASEURL = 'http://www.heeyhome.com/lpcs/home/';
var ADDRESSLISTURL = BASEURL + 'address/index'; // 地址列表
var DEFAULTADDRESSURL = BASEURL + 'address/setdefault'; // 设为默认地址

var path = 'http://www.heeyhome.com/lpcs/view/';///untitled

// var openid = 'weww1';
var openid = sessionStorage.getItem('openid');

var addressEv = {
    init: function () {
        var self = this;
        self.goBackEvent();
        self.addressManageEvent();
        self.addAddressEvent();
        self.addressList();
        self.addressDefault();
    },
    /**
     * 返回上一页
     */
    goBackEvent: function () {
        $('.go_back').click(function () {
            window.location.href = path + 'order.html';
        })
    },
    /**
     * 地址管理
     */
    addressManageEvent: function () {
        /* 地址点击切换 */
        // var $input = $('.address_detail input');
        $(document).on('click', '.address_detail input', function () {
            // $input.parent().find('em').removeClass('choose');
            // $(this).parent().find('em').addClass('choose');
            var addressObj = {};
            addressObj.name = $(this).parent().find('.address_name').html();
            addressObj.tel = $(this).parent().find('.address_tel').html();
            addressObj.room = $(this).parent().find('.address_room').html();
            addressObj.id = $(this).parent().attr('data-id');
            sessionStorage.setItem('addressObj', JSON.stringify(addressObj));//把选中的地址存到session里
            window.location.href = path + 'order.html';
        });
        // /* 默认高亮 */
        // var addressId = sessionStorage.getItem('addressId');
        // var $label = $('.address_detail label');
        // for (var i = 0; i < $label.length; i++) {
        //     if (addressId == $label.eq(i).attr('data-id')) {
        //         $label.eq(i).find('em').addClass('choose');
        //         $label.eq(i).find('input').attr('checked', 'checked');
        //     }
        // }
    },
    /**
     * 默认地址
     */
    addressDefault: function () {
        /* 默认地址点击切换 */
        $(document).on('click', '.address_default input', function () {
            var $input = $('.address_default input');
            var $defaultAddress = $(this).parents('.address_wrap').find('.address_detail label').attr('data-id');
            console.log($defaultAddress);
            $input.parent().find('em').removeClass('default_choose');
            $(this).parent().find('em').addClass('default_choose');
            addressEv.setdefaultInfoEvent($defaultAddress);
        });
    },
    /**
     * 设置默认地址
     */
    setdefaultInfoEvent: function (id) {
        $.ajax({
            url: DEFAULTADDRESSURL,
            type: "GET",
            async: true,
            dataType: 'jsonp',
            data: {
                openid: openid,
                address_id: id
            },
            success: function (data) {
                console.log(data);
                if (data.code == '000') {
                    $('.addressWrap').empty();
                    addressEv.addressList();
                } else {
                    layer.msg(data.msg);
                }
            }, error: function (data) {

            }
        });
    },
    /**
     * 新增地址
     */
    addAddressEvent: function () {
        $('#add_address').click(function () {
            sessionStorage.setItem('type', $(this).attr('data-type'));
            window.location.href = path + 'add_address.html';
        })
    },
    /**
     * 编辑地址
     */
    editAddressEvent: function () {
        $('.manage_address').click(function () {
            sessionStorage.setItem('type', $(this).attr('data-type'));
            sessionStorage.setItem('editAddressId', $(this).prev().find('label').attr('data-id'));
            window.location.href = path + 'add_address.html';
        })
    },
    /**
     * 获取地址列表
     */
    addressList: function () {
        $.ajax({
            url: ADDRESSLISTURL,
            type: "GET",
            async: true,
            data: {
                openid: openid
            },
            dataType: 'jsonp',
            success: function (data) {
                console.log(data.data);
                if (data.code == '000') {
                    var $content = $('.addressWrap');
                    $.each(data.data, function (i, v) {
                        var stitching = '<div class="address_wrap">';
                        stitching += '<div id="address_top">';
                        stitching += '<div class="address_detail clearfix">';
                        stitching += '<label data-id="' + v.address_id + '">';
                        stitching += '<em class="fl"></em>';
                        stitching += '<input class="fl" title="" type="radio" name="address">';
                        stitching += '<div class="address fl">';
                        stitching += '<p>';
                        stitching += '<span class="address_name">' + v.name + '</span> ';
                        stitching += '<span class="address_tel">' + v.phone + '</span>';
                        stitching += '</p>';
                        stitching += '<p class="address_room">' + v.area + ' ' + v.address + '' + v.room + '</p>';
                        stitching += '</div>';
                        stitching += '</label>';
                        stitching += '</div>';
                        stitching += '<div data-type="edit" class="manage_address">';
                        stitching += '<em></em>';
                        stitching += '</div>';
                        stitching += '</div>';
                        stitching += '<p class="address_default">';
                        if (v.is_default == '0') {
                            stitching += '<label class="clearFix">';
                            stitching += '<em class="fl"></em>';
                            stitching += '<input class="fl" title="" type="radio" name="default">';
                            stitching += '<span>设为默认地址</span>';
                        } else {
                            stitching += '<label class="active_label">';
                            stitching += '<em class="fl default_choose"></em>默认地址';
                        }
                        stitching += '</label>';
                        stitching += '</p>';
                        stitching += '</div>';
                        $content.append(stitching);
                    });
                    /* 默认高亮 */
                    var addressId = sessionStorage.getItem('addressId');
                    var $label = $('.address_detail label');
                    for (var i = 0; i < $label.length; i++) {
                        if (addressId == $label.eq(i).attr('data-id')) {
                            $label.eq(i).find('em').addClass('choose');
                            $label.eq(i).find('input').attr('checked', 'checked');
                        }
                    }
                    addressEv.editAddressEvent();
                } else {
                    var $addressWrap = $('.addressWrap');
                    $addressWrap.html('没有收货地址<br/>点击下方按钮新增');
                    $addressWrap.addClass('no_address');
                }
            },
            error: function (data) {
            }
        });
    }

};

$(function () {
    addressEv.init();
});