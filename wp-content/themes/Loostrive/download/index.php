<?php
date_default_timezone_set('PRC');
header("Content-Type: text/html; charset=utf-8");

$download = isset($_REQUEST["download"]) ? $_REQUEST["download"] : die("QQ:1716001590");
if (!empty($download) && ($download == "suburl" || $download == "magnet")) {
} else {
    die("Validation failure");
}
$postid = I("request.postid", 0);
$title = I("request.title");
$id = I("request.id", 0);

require_once('ctwp.php');

if ((int)$id > 0 && !empty($title)) {
    $ret = $wpdb->get_results(" select id,a,postid from " . $wpdb->prefix . "auto_movie where id='" . $id . "' limit 1 ");
    if ($ret) {
        foreach ($ret as $key1 => $value1) {
            $id = $value1->postid;
            $data_movie_a = json_decode($value1->a);
            foreach ($data_movie_a as $key2 => $value2) {
                if (trim(strtolower($value2->title)) == trim(strtolower($title))) {
                    $title = $value2->title;
                    if (!empty($value2->download)) {
                        $magnet = scraper::magnet($value2->download);
                    }
                    if (!empty($value2->suburl)) {
                        $suburl = $value2->suburl;
                    }
                }
            }
        }
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>下载<?php echo $title; ?></title>
            <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no"/>
            <style>
                html, body {
                    width: 100%;
                    height: 100%;
                    margin: 0;
                    padding: 0;
                }
                body {
                    display: flex;
                    align-items: center; /*定义body的元素垂直居中*/
                    justify-content: center; /*定义body的里的元素水平居中*/
                }
                .content {
                    min-width: 300px;
                    /*width: 50%;*/
                    /*height: 300px;*/
                    /*background: orange;*/
                }
                #main {height: 10rem;}
                #main a {position: relative;}
                #main a img {position: absolute;width: 24px;top: -1px;}
            </style>
        </head>
        <body>
        <div class="content">
            <div id="main" class="<?php echo $download; ?>">
                下载本片：<a class="download-link pass" id="<?php echo $id; ?>" data-value="<?php
                if ($download == 'magnet') {
                    echo base64_encode($magnet);
                } else {
                    echo 'javascript:;';
                }
                ?>" title="<?php echo $title; ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/loading.gif" style=""></a>
                <span class="ctwp"></span>
            </div>
            <div id="downtool" class="<?php echo $download; ?>">
                如果无法下载，请点击此处，获得下载工具<br><br>
                <a href="javascript:void(0);" onclick="window.open('https://089u.com/file/22302351-405975283','_blank');">qbittorrent(推荐)</a>&nbsp;&nbsp;&nbsp;
                <a href="javascript:void(0);" onclick="window.open('https://089u.com/file/22302351-405977621','_blank');">BitComet</a>&nbsp;&nbsp;&nbsp;
                <a href="javascript:void(0);" onclick="window.open('https://089u.com/file/22302351-405978575','_blank');">Vuze</a>&nbsp;&nbsp;&nbsp;
                <a class="pass" href="https://089u.com/f/22302351-623353431-af9d37?p=mv918" target="_blank">uTorrent</a>&nbsp;&nbsp;&nbsp;
                <a href="javascript:void(0);" onclick="window.open('https://089u.com/file/22302351-405979503','_blank');">迅雷</a>
            </div>
        </div>

        <script src="<?php echo bloginfo('stylesheet_directory'); ?>/js/jquery.min.js"></script>
        <script src="<?php echo bloginfo('stylesheet_directory'); ?>/js/ct.js"></script>
        <script src="<?php echo bloginfo('stylesheet_directory'); ?>/download/download.js"></script>
        <script>
            var _hmt = _hmt || [];
            (function () {
                var hm = document.createElement("script");
                hm.src = "https://hm.baidu.com/hm.js?9c8fead9d61297b4ec8e80142d0bed46";
                var s = document.getElementsByTagName("script")[0];
                s.parentNode.insertBefore(hm, s);
            })();
        </script>
        </body>
        </html>
        <?php
    }
} elseif ((int)$postid > 0 && !empty($title) && !empty($download)) {
    $ret = $wpdb->get_results(" select id,a,postid from " . $wpdb->prefix . "auto_movie where postid='" . $postid . "' limit 1 ");
    if ($ret) {
        foreach ($ret as $key1 => $value1) {
            $data_movie_a = json_decode($value1->a);
            foreach ($data_movie_a as $key2 => $value2) {
                if (strtolower($value2->title) == strtolower($title)) {
                    if (!empty($value2->download)) {
                        $magnet = scraper::magnet($value2->download);
                    }
                    if (!empty($value2->suburl)) {
                        $suburl = $value2->suburl;
                    }
                }
            }
        }
        if ($download == "magnet") {
            echo $magnet;
        } elseif ($download == "suburl") {
            echo $suburl;
        }
    }
}


/*-----------dump打印函数-----------*/
if (!function_exists('dump')) {
    function dump($var, $echo = true, $label = null, $strict = true) {
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
}

/*--------- I 获取参数---------*/
function I($type_key, $value = NULL) {
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
 * @param int $type   随机字串类型
 * @return string
 */
function randStr($length = 6, $type = 0) {
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

function curl($url, $method = 'GET') {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $return['exec'] = curl_exec($ch);
    $return['getinfo'] = curl_getinfo($ch);
    curl_close($ch);
    //var_dump($return);
    return $return;
}