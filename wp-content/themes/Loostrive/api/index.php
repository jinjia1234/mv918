<?php
header("Content-Type: text/html;charset=utf-8");
$api = I("request.api");
$id = I("request.id");
$token = I("request.token");
$token_server = md5($id . "abcd");
//echo $token_server;

if ($token != $token_server) {
    die("拒绝访问（".$token_server."）");
};


if ($api == "list") {
    if ($id != "0") {
        $where_str = $wpdb->prefix . "term_relationships.term_taxonomy_id in('" . $id . "') AND ";
    }
    $sort = I("request.sort");
    $sort_str = " wp_posts.ID DESC ";
    if (!empty($sort)) {
        if ($sort == 'views') {
            $sort_str = " meta_key DESC ";
        }
    }
    $record = I("request.record",25);
    $page = I("reqeust.page",1);
    $limitStart = '1'==$page ? 0 : ($page-1)*$record ;
    $limit_str = " limit {$limitStart},{$record} ";

    $SqlStr = "
SELECT
" . $wpdb->prefix . "posts.*,
" . $wpdb->prefix . "term_relationships.*,
" . $wpdb->prefix . "term_taxonomy.*,
" . $wpdb->prefix . "terms.*,
" . $wpdb->prefix . "postmeta.*,
" . $wpdb->prefix . "auto_movie.*
FROM " . $wpdb->prefix . "posts
LEFT JOIN " . $wpdb->prefix . "term_relationships ON " . $wpdb->prefix . "posts.ID = " . $wpdb->prefix . "term_relationships.object_id
LEFT JOIN " . $wpdb->prefix . "term_taxonomy ON " . $wpdb->prefix . "term_relationships.term_taxonomy_id=" . $wpdb->prefix . "term_taxonomy.term_id
LEFT JOIN " . $wpdb->prefix . "terms ON " . $wpdb->prefix . "term_relationships.term_taxonomy_id=" . $wpdb->prefix . "terms.term_id
LEFT JOIN " . $wpdb->prefix . "postmeta ON meta_key='views' AND post_id=" . $wpdb->prefix . "posts.ID
LEFT JOIN " . $wpdb->prefix . "auto_movie ON postid=" . $wpdb->prefix . "posts.ID
WHERE " . $where_str . " post_type='post' AND taxonomy='category'
ORDER BY " . $sort_str . $limit_str. "
    ";
    //var_dump($SqlStr);
    //exit;
    $ret = $wpdb->get_results($SqlStr);
    if (is_array($ret)) {
        foreach ($ret as $key => $value) {
            $b = json_decode($value->b);
            $data[$key]["id"] = $value->ID;
            //$data[$key]["category"]=$value->name;
            $data[$key]["title"] = $value->post_title;
            //$data[$key]["views"]=$value->meta_value;
            $data[$key]["posterMedium"] = $b->image;
        }
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        echo $data;
        //            dump($data);
    }
}


if ($api == "post") {
    if (!empty($id) && (int)$id > 0) {
        //        echo sha1($id);
        //        exit;
        $sql = " select " . $wpdb->prefix . "posts.*," . $wpdb->prefix . "auto_movie.* from " . $wpdb->prefix . "posts LEFT JOIN " . $wpdb->prefix . "auto_movie on " . $wpdb->prefix . "posts.ID=" . $wpdb->prefix . "auto_movie.postid where " . $wpdb->prefix . "posts.id='" . $id . "' limit 1 ; ";
        $ret = $wpdb->get_results($sql);
        //dump($ret);
        if ($ret[0]) {
            $a = json_decode($ret[0]->a);
            $b = json_decode($ret[0]->b);
            $data["id"] = $ret[0]->postid;
            $data["videoType"] = "mov";
            $data["title"] = $ret[0]->post_title;
            $data["year"] = $b->attrs->year[0];
            $data["rating"] = $b->rating->average;
            $data["imdb"] = $ret[0]->imdb;
            $cast = $b->attrs->cast;
            if($cast){
                foreach ($cast as $key => $value) {
                    $cast_str .= $value . ' / ';
                }
                if (substr($cast_str, -3) == " / ") {
                    $cast_str = substr($cast_str, 0, -3);
                }
            }
            $data["actors"] = $cast_str;
            $data["trailer"] = "";
            $data["description"] = $b->summary;
            $data["posterMedium"] = $b->image;
            $data["posterBigUrl"] = "";
            if($a){
                $i = 0;
                foreach ($a as $key => $value) {
                    $magnet = $wpdb->get_row(" select * from " . $wpdb->prefix . "auto_movie_magnet where  movieid={$ret[0]->id} and title='" . $value->title . "' ; ");
                    if ($magnet) {
                        if (class_exists('scraper')) {
                            $scraper = new scraper();
                            if($magnet->seeders >= $scraper->config['minSeeders']) {
                                $url[$i]["backdropUrl"] = scraper::magnet($value->download);
                                $url[$i]["size"] = $value->size;
                                $url[$i]["suburl"] = $value->suburl;
                                $i++;
                            }
                        }
                    }
                }
            }
            $data["url"] = $url;
            //dump($data);
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
            echo $data;
        }
    }
}

/*-----------dump打印函数-----------*/
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
 * @param int $type 随机字串类型
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