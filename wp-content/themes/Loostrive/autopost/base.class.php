<?php

class base {
    //dump打印函数
    public function dump($var, $echo = true, $label = null, $strict = true) {
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
        } else
            return $output;
    }

    // I获取参数
    public function I($type_key, $value = NULL) {
        if (empty($type_key)) return false;
        $ary = explode(".", $type_key, 2);
        if ($ary[0] == "get" || $ary[0] == "GET") {
            return isset($_GET[$ary[1]]) ? $_GET[$ary[1]] : $value;
        } else if ($ary[0] == "post" || $ary[0] == "POST") {
            return isset($_POST[$ary[1]]) ? $_POST[$ary[1]] : $value;
        } else {
            return isset($_REQUEST[$ary[1]]) ? $_REQUEST[$ary[1]] : $value;
        }
    }

    /**
     * 功能: 生成随机字符串;
     * @param int $length 随机字串长度
     * @param int $type 随机字串类型
     * @return string
     */
    public function randStr($length = 6, $type = 0) {
        switch ($type) {
            case 1:
                $string = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                break;
            case 2:
                $string = 'abcdefghijklmnopqrstuvwxyz0123456789';
                break;
            case 3:
                $string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                break;
            case 4:
                $string = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 5:
                $string = '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY';
                break;
            case 0:
            default :
                $string = '0123456789';
                break;
        }
        $string = str_split($string, 1);
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $string[rand(0, count($string) - 1)];
        }
        return $code;
    }

    /**
     * @param string $url 请求路径
     * @param array $option 数组参数 ['header','body','cookefile',cookie]
     * @return array
     */
    public function curl($url, array $option = []) {
        try {
            $ch = curl_init();
            if (stripos($url, "https://") !== false) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSLVERSION, 1);
            }
            if (isset($option['header'])) {
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $option['header']);    //提交header一维数组
            }
            if (isset($option['body'])) {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $option['body']);      //提交body二维数组
            }
            if (isset($option['cookiefile'])) {
                curl_setopt($ch, CURLOPT_COOKIEFILE, $option['cookiefile']);    //保存cookie的绝对路径
                curl_setopt($ch, CURLOPT_COOKIEJAR, $option['cookiefile']);     //读取cookie的绝对路径
            }
            if (isset($option['cookie'])) {
                curl_setopt($ch, CURLOPT_COOKIE, $option['cookie']);    //提交cookie字符串
            }
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, isset($option['followlocation']) ? $option['followlocation'] : 1);    //当遇到301,302跳转时,抓取跳转的页面
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HEADER, true);         //返回 response_header, 如果不为 true, 只会获得响应的正文
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);    //使 curl_getinfo 返回 reqeust_header
            $exec = curl_exec($ch);
            $getinfo = curl_getinfo($ch);
            $curlinfo_header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $return = array(
                'exec'    => substr($exec, $curlinfo_header_size),
                'getinfo' => $getinfo + [
                        'response_header' => substr($exec, 0, $curlinfo_header_size)
                    ],
                'error'   => curl_error($ch),
                'errno'   => curl_errno($ch),
            );
            curl_close($ch);
            return $return;
        } catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
        }
    }

    //保存日志文件
    public function logged($data = null, $filename = null, $pointer = 'start') {
        $filename = empty($filename) ? 'log.html' : $filename;
        $filelog = __DIR__ . '/' . $filename;
        if (!file_exists($filelog)) @file_put_contents($filelog, '');
        $fileLogMaxSize = 1024 * 1024 * 3;
        if (abs(@filesize($filelog)) < (int)$fileLogMaxSize) {
            switch (strtolower($pointer)) {
                case 'start':
                    @file_put_contents($filelog, date("Y-m-d H:i:s") . ' ' . $data . '<br>'.PHP_EOL . file_get_contents($filelog), LOCK_EX);
                    break;
                default:
                    @file_put_contents($filelog, date("Y-m-d H:i:s") . ' ' . $data .'<br>'. PHP_EOL, FILE_APPEND | LOCK_EX);
            }
        } else {
            @file_put_contents($filelog, date("Y-m-d H:i:s") . $data .'<br>'. PHP_EOL, LOCK_EX);
        }
    }
}