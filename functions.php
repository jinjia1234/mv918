<?php
include_once('config.php');

function textFaceAt($str=''){
    global $face;
    $old_strs = array();
    $new_strs = array();
    $results = preg_match_all('/\[CQ:face,id=(\d*)\]/i',$str,$strs);
    if($results){
        foreach($strs[0] as $key=>$value){
            $old_strs[] = $value;
            $faceid = (int)($strs[1][$key]);
            $title = '';
            $alt = '';
            if($faceid >=0 && $faceid<=170){
                $title = 'title="'.$face[$strs[1][$key]].'"';
                $alt = 'alt="'.$face[$strs[1][$key]].'"';
            }
            $new_strs[] = '<img '.$title.' src="./images/face/'.$faceid.'.gif" '.$alt.' onerror="imgerror(this);">';
        }
    }
    $results = preg_match_all('/\[CQ:at,(?:|.+?)(\d*)\]/i',$str,$strs);
    if($results){
        foreach($strs[0] as $key=>$value){
            $old_strs[] = $value;
            $new_strs[] = '@'.$strs[1][$key].' ';
        }
    }
    // dump($old_strs);
    // dump($new_strs);
    return str_replace($old_strs,$new_strs,$str);
}
function textImages($str=''){
    $old_strs = array();
    $new_strs = array();
    $results = preg_match_all('/\[CQ:image,file=(.+?)\]/i',$str,$strs);
    if($results){
        foreach($strs[0] as $key=>$value){
            $file = 'D:\Program Files\酷Q\酷Q Air CQA-图灵机器人(小灰灰)\data\image\\'.$strs[1][$key].'.cqimg';
            $file_path = iconv('utf-8','GBK',$file);
            if(file_exists($file_path)){
                $text = file_get_contents($file_path);
                $results = preg_match('/url=(.+)\r\n/i',$text,$url);
                if($results){
                    // $results = file_put_contents(time().'.png',file_get_contents($url[1]));
                    // dump($results);
                    $old_strs[] = $strs[0][$key];
                    $new_strs[] = '<a href="'.$url[1].'" target="_blank"><img src="'.$url[1].'" style="max-width:100%;"></a>';
                }
            }
        }
    }
    // $results = preg_match_all('/\[CQ:record,file=(.+?).amr\]/i',$str,$strs);
    // if($results){
        // foreach($strs[0] as $key=>$value){
            // $file = 'D:\Program Files\酷Q\酷Q Air CQA-图灵机器人(小灰灰)\data\record\\'.$strs[1][$key].'.amr';
            // $file_path = iconv('utf-8','GBK',$file);
            // if(file_exists($file_path)){
                // $old_strs[] = $strs[0][$key];
                // $new_strs[] = '<a href="'.$file.'" target="_blank">'.$strs[1][$key].'.amr</a>';
            // }
        // }
    // }
    // dump($old_strs);
    // dump($new_strs);
    $old_strs = array_unique($old_strs);
    $new_strs = array_unique($new_strs);
    if(count($new_strs)>0){
        return str_replace($old_strs,$new_strs,$str);
    }else{
        return $str;
    }
}

function textLinks($str='') {
    $old_strs = array();
    $new_strs = array();
    $results = preg_match_all('(\[[\s\S]*?\])',$str,$texts);
    if($results){
        foreach($texts[0] as $key=>$value){
            $results = preg_match_all('/(http|www).+?(?= |,|\(|])/i',$value,$text);
            if($results){
                foreach($text[0] as $k=>$v){
                    $old_strs[] = $v;
                    $new_strs[] = '<a href="'.$v.'" target="_blank">'.$v.'</a>';
                }
            }
        }
        
    }
    $results = preg_replace('(\[[\s\S]*?\])','',$str);
    if($results){
        $results = preg_match_all('/(http|www)(.+?)(?=\r|\n| |,|\(|]|$)/i',$results,$strs);
        if($results){
            foreach($strs[0] as $key=>$value){
                $old_strs[] = $value;
                $new_strs[] = '<a href="'.$value.'" target="_blank">'.$value.'</a>';
            }
        }
    }
    $old_strs = array_unique($old_strs);
    $new_strs = array_unique($new_strs);
    if(count($new_strs)>0){
        return str_replace($old_strs,$new_strs,$str);
    }else{
        return $str;
    }
}
function getline( $fp, $delim )
{
    $result = "";
    while( !feof( $fp ) )
    {
        $tmp = fgetc( $fp );
        if( $tmp == $delim )
            return $result;
        $result .= $tmp;
    }
    return $result;
}
function get_nickname($qq){
    $subject = curl("https://users.qzone.qq.com/fcg-bin/cgi_get_portrait.fcg?uins=".$qq);
    preg_match("/,\"(.*?)\",/",$subject,$matches);
    // dump($matches);
    return $matches[1];

    // $fh = fopen("https://users.qzone.qq.com/fcg-bin/cgi_get_portrait.fcg?uins=".$qq,'r');
    // if($fh){
    //     $row = iconv('GBK','UTF-8',fgets($fh));
    //     preg_match("/,\"(.*?)\",/",$row,$matches);
    //     // dump($matches);
    //     return $matches[1];
    // }


}

/*-----------dump打印函数-----------*/
function dump($var, $echo=true, $label=null, $strict=true) {
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    }else
        return $output;
}

/*--------- I 获取参数---------*/
function I($type_key,$value=NULL){
    if(empty($type_key)) return false;
    $ary=explode(".",$type_key,2);
    if($ary[0]=="get" || $ary[0]=="GET"){
        return isset($_GET[$ary[1]])?$_GET[$ary[1]]:$value;
    }else if($ary[0]=="post" || $ary[0]=="POST"){
        return isset($_POST[$ary[1]])?$_POST[$ary[1]]:$value;
    }else{
        return isset($_REQUEST[$ary[1]])?$_REQUEST[$ary[1]]:$value;
    }
}

/*--------- CURL 请求---------*/
function CURL($url, $method = 'GET', $headers = array()) {
    // $headers = array(
    //     'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
    //     // 'Accept-Encoding:gzip',
    //     'content-type:text/plain;charset=GB2312',
    //     'Accept-Language:zh-CN,zh;q=0.9',
    //     'Cache-Control:max-age=0',
    //     'Connection:keep-alive',
    //     // 'Host:users.qzone.qq.com',
    //     'Upgrade-Insecure-Requests: 1',
    //     'User-Agent: User-Agent:Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.112 Safari/537.36',
    // );

    $ch = curl_init();
    if (count($headers) > 0) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);                // 对认证证书来源的检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);                // 从证书中检查SSL加密算法是否存在
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);                // 使用自动跳转
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HEADER, 0);                        //设置头文件的信息作为数据流输出
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);            //设置获取的信息以文件流的形式返回，而不是直接输出。
    $return = [
        'error'   => curl_error($ch),
        'errno'   => curl_errno($ch),
        'exec'    => curl_exec($ch),
        'getinfo' => curl_getinfo($ch),
    ];
    curl_close($ch);
    return mb_convert_encoding($return['exec'], 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
}


function get_fcontent($url, $timeout = 5) {
    $headers = array();
    //    $headers[] = 'X-Apple-Tz: 0';
    //    $headers[] = 'X-Apple-Store-Front: 143444,12';
    $headers[] = 'Accept: text/html, application/xhtml+xml, image/jxr, */*';
    $headers[] = 'Accept-Encoding: gzip, deflate';
    $headers[] = 'Accept-Language: zh-CN';
    $headers[] = 'Accept-CONNECTION: Keep-Alive';
    //    $headers[] = 'Cache-Control: no-cache';
    //    $headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=utf-8';
    $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko';
    //    $headers[] = 'X-MicrosoftAjax: Delta=true';

    $url = str_replace("&amp;", "&", urldecode(trim($url)));
    //$cookie = tempnam ("/tmp", "CURLCOOKIE");
    $cookie = "./cookie.txt";
    $ch = curl_init(); //模拟浏览器 在HTTP请求中包含一个"User-Agent: "头的字符串。
    //curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" ); //需要获取的URL地址，也可以在 curl_init()函数中设置。
    //curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1; rv:52.0) Gecko/20100101 Firefox/52.0" ); //需要获取的URL地址，也可以在 curl_init()函数中设置。
    curl_setopt($ch, CURLOPT_URL, $url); //连接结束后保存cookie信息的文件。

    curl_setopt($ch, CURLOPT_COOKIE, "=tcc;q2bquVJn=qCBnZk87;aby=2;expla=1");
    //curl_setopt( $ch, CURLOPT_COOKIEJAR, $cookie); //启用时会将服务器服务器返回的"Location: "放在header中递归的返回给服务器，使用CURLOPT_MAXREDIRS可以限定递归返回的数量。
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); //HTTP请求头中"Accept-Encoding: "的值。支持的编码有"identity"，"deflate"和"gzip"。如果为空字符串""，请求头会发送所有支持的编码类型。在cURL 7.10中被加入。
    curl_setopt($ch, CURLOPT_ENCODING, ""); //将 curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //当根据Location:重定向时，自动设置header中的Referer:信息。
    //curl_setopt( $ch, CURLOPT_AUTOREFERER, true ); //禁用后cURL将终止从服务端进行验证。使用CURLOPT_CAINFO选项设置证书使用CURLOPT_CAPATH选项设置证书目录 如果CURLOPT_SSL_VERIFYPEER(默认值为2)被启用，CURLOPT_SSL_VERIFYHOST需要被设置成TRUE否则设置为FALSE。自cURL 7.10开始默认为TRUE。从cURL 7.10开始默认绑定安装。
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    # required for https urls  //在发起连接前等待的时间，如果设置为0，则无限等待。
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); //设置cURL允许执行的最长毫秒数。
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); //指定最多的HTTP重定向的数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的。
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //// 返回 response_header, 该选项非常重要,如果不为 true, 只会获得响应的正文
    //curl_setopt($ch, CURLOPT_HEADER, true);
    //// 是否不需要响应的正文,为了节省带宽及时间,在只需要响应头的情况下可以不要正文
    //curl_setopt($ch, CURLOPT_NOBODY, true);
    //curl_setopt( $ch, CURLOPT_PROXY, $url);
    $content = curl_exec($ch);
    //// 获得响应结果里的：头大小
    //$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    //// 根据头大小去获取头信息内容
    //$header = substr($content, 0, $headerSize);
    //dump($content);
    curl_close($ch);
    return $content;
}