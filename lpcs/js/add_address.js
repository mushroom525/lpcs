/**
 * Created by Administrator on 2017/3/30.
 */
var BASEURL = 'http://www.heeyhome.com/lpcs/home/';
var ADDADDRESSURL = BASEURL + 'address/add'; // 新增地址
var EDITADDRESSURL = BASEURL + 'address/edit'; // 编辑地址
var DELETEADDRESSURL = BASEURL + 'address/del'; // 删除地址
var ADDRESSINFOURL = BASEURL + 'address/addressinfo'; // 获取编辑地址信息

var path = 'http://www.heeyhome.com/lpcs/view/';///untitled

// var openid = 'weww1';
var openid = sessionStorage.getItem('openid');

var PHONEREG = /^(((13[0-9]{1})|(14[0-9]{1})|(17[0]{1})|(15[0-3]{1})|(15[5-9]{1})|(18[0-9]{1}))+\d{8})$/; // 验证手机号正则表达式

var addressEv = {
    init: function () {
        var self = this;
        self.goBackEvent();
        self.getSexEvent();
        self.addressEvent();
        self.mapJump();
        self.addressInfo();
        // window.onload = function () {
        //     sessionStorage.removeItem("addressInfoObj");
        // }
    },
    /**
     * 返回上一页
     */
    goBackEvent: function () {
        $('.go_back').click(function () {
            window.location.href = path + 'address.html';
        })
    },
    /**
     * 获取性别
     */
    getSexEvent: function () {
        $('.sex').click(function () {
            $('.sex').prop('checked', false);
            $(this).prop('checked', true);
            $('.sex').prev().removeClass('em_choose');
            $(this).prev().addClass('em_choose');
        })
    },
    /**
     * 地图跳转
     */
    mapJump: function () {
        $('.area').click(function () {
            // var name = $('.enter_name').val();
            // var tel = $('.contact_tel').val();
            // var room = $('.room_input').val();
            // var sex = $("input[name='sex']:checked").val();
            // var obj = {
            //     name: name,
            //     tel: tel,
            //     room: room,
            //     sex: sex
            // };
            var obj = {};
            obj.name = $('.enter_name').val();
            obj.tel = $('.contact_tel').val();
            obj.room = $('.room_input').val();
            obj.sex = $("input[name='sex']:checked").val();
            sessionStorage.setItem('dataInfo', JSON.stringify(obj));
            console.log(obj);
            window.location.href = path + 'txmap.html';
        })
    },
    /**
     * 获取数据
     */
    addressInfo: function () {
        var dataInfo = sessionStorage.getItem('dataInfo');
        sessionStorage.setItem('dataInfo', '');
        console.log(dataInfo);
        var $label = $('.contact_name label');
        if (dataInfo != '' && dataInfo != null && dataInfo != undefined) {
            $label.find('em').removeClass('em_choose');
            $label.find('input').removeAttr('checked');
            var addressInfoNew = JSON.parse(dataInfo);
            $('.enter_name').val(addressInfoNew.name);
            $('.contact_tel').val(addressInfoNew.tel);
            $('.room_input').val(addressInfoNew.room);
            if (addressInfoNew.sex == '1') {
                $('#man').prop('checked', 'checked');
                $('#man').prev().addClass('em_choose');
            } else {
                $('#women').prop('checked', 'checked');
                $('#women').prev().addClass('em_choose');
            }
        }
        var obj = sessionStorage.addressInfoObj;
        // sessionStorage.removeItem("addressInfoObj");
        if (obj != null && obj != undefined && obj != "") {
            var valObj = JSON.parse(obj);
            if (valObj.poiname != null && valObj.poiname != "") {
                if (valObj.poiname == '我的位置') {
                    $('.Jtxmap').html(valObj.poiaddress);
                    $('.Jaddress').val(valObj.cityname + valObj.poiaddress);
                } else {
                    $('.Jtxmap').html(valObj.poiname);
                    $('.Jaddress').val(valObj.poiaddress);
                }
            }
        }
    },
    /**
     * 调取地址接口
     */
    addressEvent: function () {
        var type = sessionStorage.getItem('type');
        console.log(type);
        if (type == 'add') {
            $('.order_span').html('添加收货地址');
            $('.right_i').removeClass('delete');
            addressEv.addAddress();
        } else {
            $('.order_span').html('编辑收货地址');
            $('.right_i').addClass('delete');
            addressEv.editAddressInfo();
            addressEv.editAddress();
            addressEv.deleteAddress();
        }
    },
    /**
     * 获取编辑地址信息
     */
    editAddressInfo: function () {
        var address_id = sessionStorage.getItem('editAddressId');
        var $label = $('.contact_name label');
        console.log(address_id);

        $.ajax({
            url: ADDRESSINFOURL,
            type: "GET",
            async: true,
            data: {
                address_id: address_id
            },
            dataType: 'jsonp',
            success: function (data) {
                if (data.code == '000') {
                    console.log(data.data);
                    $('.enter_name').val(data.data.name);
                    $('.contact_tel').val(data.data.phone);
                    var obj = sessionStorage.addressInfoObj;
                    sessionStorage.removeItem("addressInfoObj");
                    if (obj != null && obj != undefined && obj != "") {
                        var valObj = JSON.parse(obj);
                        if (valObj.poiname != null && valObj.poiname != "") {
                            if (valObj.poiname == '我的位置') {
                                $('.Jtxmap').html(valObj.poiaddress);
                                $('.Jaddress').val(valObj.cityname + valObj.poiaddress);
                            } else {
                                $('.Jtxmap').html(valObj.poiname);
                                $('.Jaddress').val(valObj.poiaddress);
                            }
                        }
                    } else {
                        $('.area').html(data.data.area);
                        $('.address_input').val(data.data.address);
                    }
                    $('.room_input').val(data.data.room);
                    $label.find('em').removeClass('em_choose');
                    $label.find('input').removeAttr('checked');
                    if (data.data.sex == '2') {
                        $('#women').prop('checked', 'checked');
                        $('#women').prev().addClass('em_choose');
                    } else {
                        $('#man').prop('checked', 'checked');
                        $('#man').prev().addClass('em_choose');
                    }
                } else {
                    layer.msg(data.msg);
                }
            },
            error: function (data) {
            }
        });
    },
    /**
     * 添加地址
     */
    addAddress: function () {
        $('.add_button').click(function () {
            var $name = $('.enter_name').val();//姓名
            var $tel = $('.contact_tel').val();//电话
            var $area = $('.area').html();//小区/大厦/学校
            var $address_input = $('.address_input').val();//详细地址
            var $room_input = $('.room_input').val();//门牌号
            if ($name == '' || $name == null) {
                layer.msg('请输入姓名');
                return false;
            } else if ($tel == '' || $tel == null) {
                layer.msg('请输入手机号');
                return false;
            } else if (!PHONEREG.test($tel)) {
                layer.msg('手机号码格式不正确');
                return false;
            } else if ($area == '' || $area == null) {
                layer.msg('请输入小区');
                return false;
            } else if ($address_input == '' || $address_input == null) {
                layer.msg('请输入详细地址');
                return false;
            } else if ($room_input == '' || $room_input == null) {
                layer.msg('请输入门牌号');
                return false;
            } else {
                $.ajax({
                    url: ADDADDRESSURL,
                    type: "GET",
                    async: true,
                    data: {

                        openid: openid,
                        name: $name,
                        sex: $("input[name='sex']:checked").val(),
                        phone: $tel,
                        area: $area,
                        address: $address_input,
                        room: $room_input
                    },
                    dataType: 'jsonp',
                    success: function (data) {
                        if (data.code == '000') {
                            window.location.href = path + 'address.html';
                        } else {
                            layer.msg(data.msg);
                        }
                    },
                    error: function (data) {
                    }
                });
            }
        })
    },
    /**
     * 编辑地址
     */
    editAddress: function () {
        $('.add_button').click(function () {
            var address_id = sessionStorage.getItem('editAddressId');
            var $name = $('.enter_name').val();//姓名
            var $tel = $('.contact_tel').val();//电话
            var $area = $('.area').html();//小区/大厦/学校
            var $address_input = $('.address_input').val();//详细地址
            var $room_input = $('.room_input').val();//门牌号
            if ($name == '' || $name == null) {
                layer.msg('请输入姓名');
                return false;
            } else if ($tel == '' || $tel == null) {
                layer.msg('请输入手机号');
                return false;
            } else if (!PHONEREG.test($tel)) {
                layer.msg('手机号码格式不正确');
                return false;
            } else if ($area == '' || $area == null) {
                layer.msg('请输入小区');
                return false;
            } else if ($address_input == '' || $address_input == null) {
                layer.msg('请输入详细地址');
                return false;
            } else if ($room_input == '' || $room_input == null) {
                layer.msg('请输入门牌号');
                return false;
            } else {
                $.ajax({
                    url: EDITADDRESSURL,
                    type: "GET",
                    async: true,
                    data: {
                        address_id: address_id,
                        name: $name,
                        sex: $("input[name='sex']:checked").val(),
                        phone: $tel,
                        area: $area,
                        address: $address_input,
                        room: $room_input
                    },
                    dataType: 'jsonp',
                    success: function (data) {
                        if (data.code == '000') {
                            window.location.href = path + 'address.html';
                        } else {
                            layer.msg(data.msg);
                        }
                    },
                    error: function (data) {
                    }
                });
            }

        })
    },
    /**
     * 删除地址
     */
    deleteAddress: function () {
        $('.delete').click(function () {
            var address_id = sessionStorage.getItem('editAddressId');
            $.ajax({
                url: DELETEADDRESSURL,
                type: "GET",
                async: true,
                data: {
                    address_id: address_id
                },
                dataType: 'jsonp',
                success: function (data) {
                    if (data.code == '000') {
                        window.location.href = path + 'address.html';
                    } else {
                        layer.msg(data.msg);
                    }
                },
                error: function (data, a, b) {
                }
            });
        })
    }
};

$(function () {
    addressEv.init();
});