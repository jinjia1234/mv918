<?php
/**
 * 兼容宝塔WebHook插件只支持GET访问
 */
$access_key = $_REQUEST['access_key'] ?? '';
$param = $_REQUEST['param'] ?? '';

if ($access_key && $param) {
    $url = "http://127.0.0.1:8888/hook?access_key={$access_key}&param={$param}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type:application/json;", "Accept:application/json"));
    $output = curl_exec($ch);
    curl_close($ch);
    echo $output;
}