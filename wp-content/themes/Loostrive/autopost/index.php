<?php
$space_list = "00:50:01";              //采集列表间隔时间
$space_post = "00:00:03";              //采集内容间隔时间
$makeNode = '2017-10-11 11:21:00';          //此时间之前重新生成

$seeders = 10;    //种子连接数 大于 10
$leechers = 3;    //种子下载数 大于 10
$size_min = 600;    //种子文件体积 最小数 单位M
$size_max = 99096;    //种子文件体积 最大数 单位M

$auto = isset($_REQUEST["auto"]) ? $_REQUEST["auto"] : die("QQ:1716001590");
if (!empty($auto) && $auto != "abcd") {
    die("Validation failure");
}
date_default_timezone_set('PRC');


var_dump(sanitize_title('你好'));
exit;
function loadprint( $class ) {
    $file = $class . '.class.php';
    if ( is_file(__DIR__.'/'.$file) ) {
        require_once($file);
    }
}
spl_autoload_register('loadprint');

$base = new base();
$autopost = new autopost();
new index([
    'wpdb'   => $wpdb,
    'pirate' => new getPirate(),
]);
exit;
$ret = $wpdb->query(" update " . $wpdb->prefix . "auto_list set autoTime=now() where autoTime is null ");
if ($ret) die;

$ret = $wpdb->get_results(" select id,term,url,autoTime,timediff(now(),autoTime) from " . $wpdb->prefix . "auto_list where timediff(now(),autoTime)>'" . $space_list . "' order by autoTime limit 1 ");
if ($ret) {
    $base->logged('采集 auto_lits '.json_encode($ret[0],320));
    $wpdb->query(" update " . $wpdb->prefix . "auto_list set autoTime=now() where url='" . $ret[0]->url . "' ");
    // dump($ret[0]);
    if (strpos($ret[0]->url, "torrentapi.org") > 0) {
        $url = $ret[0]->url . "&token=" . getToken();
        if (stripos($url, "app_id=") <= 0) {
            $url .= "&app_id=MyRarBGApp";
        }
        dump($url);
        $content = get_fcontent($url, 10);
        $results = json_decode($content);
        $results = $results->torrent_results;
        //dump($results);

        $db = new MySqli(constant("DB_HOST"), constant("DB_USER"), constant("DB_PASSWORD"), constant("DB_NAME"));
        !mysqli_connect_error() or die("连接数据库错误： " . mysqli_connect_error());
        mysqli_query($db, "set names utf8");
        $sql = " insert into " . $wpdb->prefix . "auto_movie (term,imdb,a) VALUES (?,?,?)";
        $stmt1 = $db->prepare($sql);
        $stmt1->bind_param("dss", $term, $imdb, $a);
        $sql = " update " . $wpdb->prefix . "auto_movie set a=?,a_upTime=? where id=?; ";
        $stmt2 = $db->prepare($sql);
        $stmt2->bind_param("ssd", $a, $a_upTime, $id);

        if ($results) {
            foreach ($results as $key1 => $value1) {
                // if($key1>10){exit;};
                if (empty($value1->episode_info->imdb)) {
                    continue;
                }
                if ($value1->seeders <= $seeders) {
                    continue;
                }
                if ($value1->leechers < $leechers) {
                    continue;
                }
                if ($value1->size < ($size_min * 1024) * 1024 or $value1->size > ($size_max * 1024) * 1024) {
                    continue;
                }

                $ret_movie = $wpdb->get_results(" select * from " . $wpdb->prefix . "auto_movie where imdb='" . $value1->episode_info->imdb . "'; ");
                if ($ret_movie) {
                    foreach ($ret_movie as $key2 => $value2) {
                        // dump($value1);
                        // dump($ret_movie);
                        $data_movie_a = json_decode($value2->a);
                        $newData = true;
                        foreach ($data_movie_a as $key3 => $value3) {
                            // dump("采集".$value1->download);
                            // dump("保存".$value3->download);
                            if ($value1->download == $value3->download) {
                                $newData = false;
                                continue;
                            }
                        }
                        if ($newData) {
                            $dataTemp = array();
                            foreach ($data_movie_a as $key4 => $value4) {
                                $dataTemp[$key4]["title"] = $value4->title;
                                $dataTemp[$key4]["category"] = $value4->category;
                                $dataTemp[$key4]["download"] = $value4->download;
                                $dataTemp[$key4]["seeders"] = $value4->seeders;
                                $dataTemp[$key4]["leechers"] = $value4->leechers;
                                $dataTemp[$key4]["size"] = $value4->size;
                                $dataTemp[$key4]["pubdate"] = $value4->pubdate;
                            }
                            $key = count($dataTemp);
                            $id = $value2->id;
                            $a_upTime = date("Y-m-d H:i:s");
                            $dataTemp[$key]["title"] = $value1->title;
                            $dataTemp[$key]["category"] = $value1->category;
                            $dataTemp[$key]["download"] = $value1->download;
                            $dataTemp[$key]["seeders"] = $value1->seeders;
                            $dataTemp[$key]["leechers"] = $value1->leechers;
                            $dataTemp[$key]["size"] = $value1->size;
                            $dataTemp[$key]["pubdate"] = $value1->pubdate;
                            //dump($dataTemp);
                            $a = json_encode($dataTemp, JSON_UNESCAPED_UNICODE);
                            $stmt2->execute();
                            echo "QQ:1716001590 => a更新 " . $value1->title . "<br>";
                        } else {
                            echo "QQ:1716001590 => a地址重复不用更新 imdb:" . $value2->imdb . "<br>";
                        }
                    }
                } else {
                    $term = $ret[0]->term;
                    $imdb = $value1->episode_info->imdb;
                    $data[0]["title"] = $value1->title;
                    $data[0]["category"] = $value1->category;
                    $data[0]["download"] = $value1->download;
                    $data[0]["seeders"] = $value1->seeders;
                    $data[0]["leechers"] = $value1->leechers;
                    $data[0]["size"] = $value1->size;
                    $data[0]["pubdate"] = $value1->pubdate;
                    $a = json_encode($data, JSON_UNESCAPED_UNICODE);
                    $stmt1->execute();
                    echo "QQ:1716001590 => a采集 " . $value1->title . "<br>";
                }
            }
        } else {
            die('QQ:1716001590 => a获取列表数据失败 ' . $url . '<br>');
        }
    }else{
    //} elseif (strpos($ret[0]->url, "pirateproxy.id") > 0 || strpos($ret[0]->url, "thehiddenbay.com") > 0       ) {
        $getPirate = new getPirate();
        $getPirate->config['limit'] = 10000;
        $result = $getPirate->get_list($ret[0]->url,true);
        // dump($result);
        if($result){

            $db = new MySqli(constant("DB_HOST"), constant("DB_USER"), constant("DB_PASSWORD"), constant("DB_NAME"));
            !mysqli_connect_error() or die("连接数据库错误： " . mysqli_connect_error());
            mysqli_query($db, "set names utf8");
            $sql = " insert into " . $wpdb->prefix . "auto_movie (term,imdb,a) VALUES (?,?,?)";
            $stmt1 = $db->prepare($sql);
            $stmt1->bind_param("dss", $term, $imdb, $a);
            $sql = " update " . $wpdb->prefix . "auto_movie set a=?,a_upTime=? where id=?; ";
            $stmt2 = $db->prepare($sql);
            $stmt2->bind_param("ssd", $a, $a_upTime, $id);

            foreach ($result as $key=>$value1){
                $value1 = (object)$value1;
                if (empty($value1->imdb)) continue;
                if (!$autopost->filter_douban_imdb($value1->imdb)) continue;
                if ($value1->seeders <= $seeders) continue;
                if ($value1->leechers < $leechers) continue;
                if (!empty($size_min) && !empty($size_max) && !empty($value1->size) && preg_match('/(\d\.\d{0,2})(MB|GB|TB|PB|EB|ZB|YB)?/', $value1->size, $sizes)) {
                    $size = $sizes[1];
                    for ($i = 1; $i <= array_search($sizes[2], ['MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']); $i++) $size = $size * 1024;
                    if ($size < $size_min or $size > $size_max) continue;
                }

                $ret_movie = $wpdb->get_results(" select * from " . $wpdb->prefix . "auto_movie where imdb='" . $value1->imdb . "'; ");
                if ($ret_movie) {
                    foreach ($ret_movie as $key2 => $value2) {
                        // dump($value1);
                        // dump($ret_movie);
                        $data_movie_a = json_decode($value2->a);
                        $newData = true;
                        foreach ($data_movie_a as $key3 => $value3) {
                            // dump("采集".$value1->magnet);
                            // dump("保存".$value3->download);
                            if (substr($value1->magnet,0,60) == substr($value3->download,0,60)) {
                                $newData = false;
                                continue;
                            }
                        }
                        if ($newData) {
                            $dataTemp = array();
                            $i = 0;
                            foreach ($data_movie_a as $key4 => $value4) {
                                $dataTemp[$i]["title"] = $value4->title;
                                $dataTemp[$i]["download"] = $value4->download;
                                $dataTemp[$i]["seeders"] = $value4->seeders;
                                $dataTemp[$i]["leechers"] = $value4->leechers;
                                $dataTemp[$i]["size"] = $value4->size;
                                $dataTemp[$i]["pubdate"] = $value4->pubdate;
                                $i++;
                            }
                            $key = count($dataTemp);
                            $id = $value2->id;
                            $a_upTime = date("Y-m-d H:i:s");
                            $dataTemp[$key]["title"] = $value1->title;
                            $dataTemp[$key]["download"] = $value1->magnet;
                            $dataTemp[$key]["seeders"] = $value1->seeders;
                            $dataTemp[$key]["leechers"] = $value1->leechers;
                            $dataTemp[$key]["size"] = round(($size * 1204) * 1024);
                            $dataTemp[$key]["pubdate"] = $value1->pubdate;
                            // dump($dataTemp);
                            $a = json_encode($dataTemp, 320);
                            $stmt2->execute();
                            echo "QQ:1716001590 => a更新 " . $value1->title . "<br>";
                        } else {
                            echo "QQ:1716001590 => a地址重复不用更新 imdb:" . $value2->imdb . "<br>";
                        }
                    }
                }else{
                    $term = $ret[0]->term;
                    $imdb = $value1->imdb;
                    $a = json_encode([[
                                          'title'    => $value1->title,
                                          'download' => $value1->magnet,
                                          'seeders'  => $value1->seeders,
                                          'leechers' => $value1->leechers,
                                          'size'     => round(($size * 1204) * 1024),
                                          'pubdate'  => $value1->update,
                                      ]], 320);
                    $stmt1->execute();
                    echo "QQ:1716001590 => a采集 " . $value1->title . "<br>";
                }
            }
        }
    }

} else {
    $ret_count = $wpdb->get_results(" select count(*)as iscount,(select count(*) from " . $wpdb->prefix . "auto_movie where b_addTime is NULL or b_addTime='0000-00-00 00:00:00')as isnull from " . $wpdb->prefix . "auto_movie ");
    if ($ret_count[0]->iscount == $ret_count[0]->isnull) {
        $ret = $wpdb->get_results(" select * from " . $wpdb->prefix . "auto_movie where imdb is not null and imdb<>'' and (b_addTime is null or b_addTime='0000-00-00 00:00:00') order by rand() limit 1 ");
    } else {
        $ret = $wpdb->get_results(" select " . $wpdb->prefix . "auto_movie.*,(select b_addTime from " . $wpdb->prefix . "auto_movie order by b_addTime desc limit 1),timediff(now(),(select b_addTime from " . $wpdb->prefix . "auto_movie order by b_addTime desc limit 1)) from " . $wpdb->prefix . "auto_movie where imdb is not null and imdb<>'' and timediff(now(),(select b_addTime from " . $wpdb->prefix . "auto_movie order by b_addTime desc limit 1))>'" . $space_post . "' and (b_addTime is null or b_addTime='0000-00-00 00:00:00') order by rand() limit 1 ");
    }
    if ($ret) {
        // dump($ret);
        $url = 'https://api.douban.com/v2/movie/imdb/' . $ret[0]->imdb.'?apikey=0df993c66c0c636e29ecbb5344252a4a';
        $content = get_fcontent($url, 10);
        $results = json_decode($content);
        if ($results->code == 5000) {
            $ret_del = $wpdb->query(" delete from " . $wpdb->prefix . "auto_movie where id='" . $ret[0]->id . "'; ");
            if ($ret_del) {
                echo "豆瓣电影没有发现 " . $ret[0]->a_imdb . " 删除该条记录 成功：\r\n";
                dump($ret);
            } else {
                echo "豆瓣电影没有发现 " . $ret[0]->a_imdb . " 删除该条记录 失败：\r\n";
                dump($ret);
            }
            exit;
        } elseif ($results->rating) {
            // dump($results);
            $data["id"] = explode("/", $results->id)[4];
            $data["alt_title"] = $results->alt_title;
            $data["title"] = $results->title;
            $image = str_replace("/movie_poster_cover/ipst/", "/movie_poster_cover/lpst/", $results->image);
            $image = str_replace("/view/photo/s_ratio_poster/public/", "/view/movie_poster_cover/lpst/public/", $image);
            $data["image"] = $image;
            $data["summary"] = $results->summary;
            $data["rating"]["average"] = $results->rating->average;
            $data["rating"]["numRaters"] = $results->rating->numRaters;
            foreach ((array)$results->author as $key => $value) {
                $data["author"][$key]["name"] = $results->author[$key]->name;
            }
            foreach ((array)$results->attrs->website as $key => $value) {
                $data["attrs"]["website"][$key] = $results->attrs->website[$key];
            }
            foreach ((array)$results->attrs->pubdate as $key => $value) {
                $data["attrs"]["pubdate"][$key] = $results->attrs->pubdate[$key];
            }
            foreach ((array)$results->attrs->language as $key => $value) {
                $data["attrs"]["language"][$key] = $results->attrs->language[$key];
            }
            foreach ((array)$results->attrs->country as $key => $value) {
                $data["attrs"]["country"][$key] = $results->attrs->country[$key];
            }
            foreach ((array)$results->attrs->writer as $key => $value) {
                $data["attrs"]["writer"][$key] = $results->attrs->writer[$key];
            }
            foreach ((array)$results->attrs->director as $key => $value) {
                $data["attrs"]["director"][$key] = $results->attrs->director[$key];
            }
            foreach ((array)$results->attrs->cast as $key => $value) {
                $data["attrs"]["cast"][$key] = $results->attrs->cast[$key];
            }
            foreach ((array)$results->attrs->year as $key => $value) {
                $data["attrs"]["year"][$key] = $results->attrs->year[$key];
            }
            foreach ((array)$results->attrs->movie_type as $key => $value) {
                $data["attrs"]["movie_type"][$key] = $results->attrs->movie_type[$key];
            }
            foreach ((array)$results->attrs->movie_duration as $key => $value) {
                $data["attrs"]["movie_duration"][$key] = $results->attrs->movie_duration[$key];
            }
            foreach ((array)$results->tags as $key => $value) {
                $data["tags"][$key] = $value->name;
            }
            //dump($data);

            $db = new MySqli(constant("DB_HOST"), constant("DB_USER"), constant("DB_PASSWORD"), constant("DB_NAME"));
            !mysqli_connect_error() or die("连接数据库错误： " . mysqli_connect_error());
            mysqli_query($db, "set names utf8");
            $sql = " update " . $wpdb->prefix . "auto_movie set b=?,b_addTime=? where id=?; ";
            $stmt1 = $db->prepare($sql);
            $stmt1->bind_param("ssd", $b, $b_addTime, $id);
            $b = json_encode($data, JSON_UNESCAPED_UNICODE);
            $b_addTime = date("Y-m-d H:i:s");
            $id = $ret[0]->id;
            $stmt1->execute();

            echo "QQ:1716001590 => b采集 " . $results->alt_title;
            if (class_exists('scraper')) {
                $result = new scraper();
                $result->id = $ret[0]->id;
                echo $result->execute();
            } else {
                echo '采集成功，但未检测磁力，请启用scraper插件';
            }
            exit;
        }

    } else {
        $ret = $wpdb->get_results(" select * from " . $wpdb->prefix . "auto_movie where a is not null and b is not null and postid>0 and makeTime is not null and (a_upTime>makeTime or makeTime<'" . $makeNode . "') limit 1; ");
        if ($ret) {
            echo "QQ:1716001590 => c更新 " . $ret[0]->postid . " ";
            if (class_exists('scraper')) {
                $result = new scraper();
                $result->id = $ret[0]->id;
                if(!empty($_GET['postid'])){
                    $row = $wpdb->get_row(" select * from " . $wpdb->prefix . "auto_movie where a is not null and b is not null and postid>0 and makeTime is not null and postid=".$_GET['postid']);
                    $result->id = $row->id;
                }
                echo $result->execute();
            } else {
                echo '请启用scraper插件';
            }
            exit;
        } else {
            if (class_exists('scraper')) {
                $result = new scraper();
                echo $result->execute();
                $base->logged('检测磁力');
            } else {
                echo '请启用scraper插件';
            }
        }
    }
}
exit;


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


function getToken() {
    $tokenFile = __DIR__ . "/token.json";         //缓存文件名;
    $myFile = fopen($tokenFile, "a");       //如果文件不存在则尝试创建
    $data = (object)json_decode(file_get_contents($tokenFile));
    if ($data->expire_time <= time() or !$data->expire_time) {
        $url = "https://torrentapi.org/pubapi_v2.php?app_id=MyRarBGApp&get_token=get_token";
        $token = curl($url);
        $token = json_decode($token);
        if ($token->token) {
            $data->token = $token->token;
            $data->expire_time = time() + 900;
            $fp = fopen($tokenFile, "w");
            fwrite($fp, json_encode($data));
            fclose($fp);
        }
        sleep(1);
    }
    return $data->token;
}

function curl($url, $method = 'GET') {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko',
    ));
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
    return $return['exec'];
}
