<?php
//设置报错级别，忽略警告，设置字符
error_reporting(E_ALL || ~E_NOTICE);
header("Content-type:text/html; charset=utf-8");
require_once "jssdk.php";
$jssdk = new JSSDK("wx4c0ce3db77b9283a", "ed96ca78967a6dc6ea57ea821c05d637");
$signPackage = $jssdk->GetSignPackage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!--因为在手机中，所以添加viewport-->

    <script src="http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
    <link href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
    <script type="application/javascript" src="http://apps.bdimg.com/libs/jquery/1.6.4/jquery.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <title>微信测试</title>
</head>
<body>
<div class="panel panel-success">
    <div class="panel-heading">
        <h3 class="panel-title">
            投饲管理.
        </h3>
    </div>
    <div class="panel-body">
        <div class="form-group text-center">
            <button class="btn btn-info btn-lg" id="feeding_picture" disabled="disabled">饲料拍照</button>
        </div>
        <div class="form-group text-center">
            <div id="feeding_img" src=""></div>
        </div><hr>
        <div class="form-group">
            <button class="btn btn-default" id="feeding_feed">饲料扫描</button>
            <input type="text" id="feeding_feed_name" class="form-control form-control-horizonal">
        </div>

        <div class="form-group">
            <button class="btn btn-default" id="feeding_pool">池塘扫描</button>
            <input type="text" id="feeding_pool_name" class="form-control form-control-horizonal">
        </div>

        <div class="form-group">
            <label for="">网箱</label>
            <select class="form-control" id="feeding_cage_id" >
                <option value="0">请选择网箱</option>
                <volist name="cage" id="caa">
                    <option value="{$caa.cage_id}">{$caa.cage_rowname}</option>
                </volist>
            </select>
        </div>
        <div class="form-group">
            <label for="">投饲数量</label>
            <input type="text" class="form-control" value="" id="feeding_number" >
        </div>
        <label class="control-label" for="input01" name="time">当前时间: {$time} </label>
        <div class="form-group hidden">
            <input type="text" class="form-control" value="" name="feeding_pool_id" id="feeding_pool_id" >
        </div>
        <div class="form-group hidden">
            <input type="text" class="form-control" value="" name="feeding_feed_id" id="feeding_feed_id" >
        </div>
        <div class="form-group hidden">
            <input type="text" class="form-control" value="" name="feeding_pool_img" id="feeding_pool_img">
        </div>
        <div class="form-group">
            <button id="feeding_submit" class="btn btn btn-success">&nbsp;提&nbsp;交&nbsp;</button>
        </div>
    </div>
</div>
</block>
<block name="rightcolumn">

</block>
<block name="script">

    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script>
        wx.config({
            debug: false,
            appId: '{$signPackage["appId"]}',
            timestamp: '{$signPackage["timestamp"]}',
            nonceStr: '{$signPackage["nonceStr"]}',
            signature: '{$signPackage["signature"]}',
            jsApiList: [
                'checkJsApi',
                'scanQRCode',
                'uploadImage',
                'getLocation',
                'chooseImage'
            ]
        });
        wx.ready(function () {

            $("#feeding_pool").click(function() {

                wx.scanQRCode({ // 默认为0，扫描结果由微信处理，1则直接返回扫描结果
                    needResult : 1,
                    desc : 'scanQRCode desc',
                    success : function(res) { //扫码后获取结果参数赋值给Input var
                        url = res.resultStr; //商品条形码，取","后面的
                        var n = url.split(",");
                        $('#feeding_pool_id').val(n[0]);
                        $('#feeding_pool_name').val(n[1]);
                    }
                });
            });
            $("#feeding_feed").click(function() {

                wx.scanQRCode({ // 默认为0，扫描结果由微信处理，1则直接返回扫描结果
                    needResult : 1,
                    desc : 'scanQRCode desc',
                    success : function(res) { //扫码后获取结果参数赋值给Input var
                        url = res.resultStr; //商品条形码，取","后面的

                        var n = url.split(" ");
                        $('#feeding_feed_id').val(n[5].split(":")[1]);
                        $('#feeding_feed_name').val(n[1].split(":")[1]);
                    }
                });
            });


            var images = {
                localId: [],
                serverId: []
            };
            $("#feeding_picture").click(function() {

                wx.chooseImage({
                    count: 1, // 默认9
                    sizeType: ['original'], // 可以指定是原图还是压缩图，默认二者都有
                    sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                    success: function (res) {
                        images.localId = res.localIds;
                        // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                        $.each( res.localIds, function(i, n){
                            $("#feeding_img").append('<img  width="250" height="250" src="'+n+'" /> <br />');
                        });
                        if (images.localId.length == 0) {
                            alert('请先使用 chooseImage 接口选择图片');
                            return;
                        }
                        var i = 0, length = images.localId.length;
                        images.serverId = [];
                        function upload() {
                            wx.uploadImage({
                                localId: images.localId[i],
                                success: function (res) {
                                    i++;
                                    images.serverId.push(res.serverId);
                                    if (i < length) {
                                        upload();
                                    }
                                    var url = "{:U('Home/Wechat/savePic/serverId/"+ res.serverId +"')}";
                                    //alert(res.serverId);
                                    //拍照逻辑问题
                                    $.get(url, function(data){

                                        if(data['status']) {
                                            alert('图片已保存');

                                        }
                                        else
                                            alert('图片上传失败');
                                        $('#feeding_pool_img').val(data['pool_img']);
                                    },'json');
                                },
                                fail: function (res) {
                                    alert(JSON.stringify(res));
                                }
                            });
                        }
                        upload();
                    }
                });
            });

    });
</script>
</body>
</html>