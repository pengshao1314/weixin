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
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <title>微信测试</title>
</head>
<body>
<button id="weixin" style="display: block;margin: 2em auto">微信接口测试</button>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
    wx.config({
        debug: true, //调试阶段建议开启
        appId: '<?php echo $signPackage["appId"];?>',
        timestamp: <?php echo $signPackage["timestamp"];?>,
        nonceStr: '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: [
            /*
             * 所有要调用的 API 都要加到这个列表中
             * 这里以图像接口为例
             */
            "chooseImage",
            "previewImage",
            "uploadImage",
            "downloadImage"
        ]
    });
    var btn = document.getElementById('weixin');
    //定义images用来保存选择的本地图片ID，和上传后的服务器图片ID
    var images = {
        localId: [],
        serverId: []
    };
    wx.ready(function () {
        // 在这里调用 API
        btn.onclick = function(){
            wx.chooseImage ({
                success : function(res){
                    images.localId = res.localIds;  //保存到images
                    // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                }
            });
        }
        document.getElementById('upload').onclick = function(){
            var i = 0, len = images.localId.length;
            function wxUpload(){
                wx.uploadImage({
                    localId: images.localId[i], // 需要上传的图片的本地ID，由chooseImage接口获得
                    isShowProgressTips: 1, // 默认为1，显示进度提示
                    success: function (res) {
                        i++;
                        //将上传成功后的serverId保存到serverid
                        images.serverId.push(res.serverId);
                        if(i < len){
                            wxUpload();
                        }
                    }
                });
            }
            wxUpload();
        }
        document.getElementById('getServices').onclick = function(){
            alert(images.serverId);
        }
    });
</script>
</body>
</html>