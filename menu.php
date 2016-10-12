<?php

   $appId=wx4c0ce3db77b9283a;
   $appSecret=ed96ca78967a6dc6ea57ea821c05d637;
    // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
    $data = json_decode(file_get_contents("access_token.json"));
    if ($data->expire_time < time()) {
        // 如果是企业号用以下URL获取access_token
        // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
        $res = json_decode($this->httpGet($url));
        $access_token = $res->access_token;
        if ($access_token) {
            $data->expire_time = time() + 7000;
            $data->access_token = $access_token;
            $fp = fopen("access_token.json", "w");
            fwrite($fp, json_encode($data));
            fclose($fp);
        }
    } else {
        $access_token = $data->access_token;
    }



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