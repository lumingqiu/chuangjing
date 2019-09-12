<?php
    $host = 'https://dwz.cn';
    $path = '/admin/v2/create';
    $url = $host . $path;
    $method = 'POST';
    $content_type = 'application/json';
    
    // TODO: 设置Token
    $token = '53b8d92ce7c0a94e8c6494970b6160e0';
    
    // TODO：设置待注册长网址
    $bodys = array('url'=>'https://m.tb.cn/h.eOIZihu?sm=e78d83', 'TermOfValidity'=>'1-year');
    
    // 配置headers 
    $headers = array('Content-Type:'.$content_type, 'Token:'.$token);
    
    // 创建连接
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($bodys));
    
    // 发送请求
    $response = curl_exec($curl);
	
if(curl_exec($curl) === false){
  echo 'Curl error: ' . curl_error($curl);
}
    curl_close($curl);
    
    // 读取响应
    var_dump($response);
?>
