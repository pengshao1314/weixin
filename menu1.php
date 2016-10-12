<?php
require_once "jssdk.php";
$menu = new JSSDK("wx4c0ce3db77b9283a", "ed96ca78967a6dc6ea57ea821c05d637");
$access_token = $menu->getAccesstoken();

$jsonmenu = '{
"button":[
{
"name":"天气预报",
"sub_button":[
{
"type":"click",
"name":"北京天气",
"key":"天气北京"
},
{
"type":"click",
"name":"上海天气",
"key":"天气上海"
},
{
"type":"click",
"name":"广州天气",
"key":"天气广州"
},
{
"type":"click",
"name":"深圳天气",
"key":"天气深圳"
},
{
"type":"view",
"name":"本地天气",
"url":"http://m.hao123.com/a/tianqi"
}]


},
{
"name":"方倍工作室",
"sub_button":[
{
"type":"click",
"name":"公司简介",
"key":"company"
},
{
"type":"click",
"name":"趣味游戏",
"key":"游戏"
},
{
"type":"click",
"name":"讲个笑话",
"key":"笑话"
}]


}]
}';


$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
$result = https_request($url, $jsonmenu);
var_dump($result);

function https_request($url,$data = null){
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
if (!empty($data)){
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
}
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($curl);
curl_close($curl);
return $output;
}

?>