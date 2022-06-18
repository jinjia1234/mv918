<?php
$space_list = "02:00:00" ;  //采集列表间隔时间
$space_post = "00:50:00" ;  //采集内容间隔时间
//$makeNode = '2017-10-11 11:21:00';   
//此时间之前重新生成

$seeders = 200 ;     //种子连接数 大于 10
$leechers = 3 ;    //种子下载数 大于 10
$size_min = 1400 ;    //种子文件体积 最小数 单位M
$size_max = 80096 ;    //种子文件体积 最大数 单位M

$GLOBALS['term_arr'] = array(       //自动分类
    '1' => '动作',
    '2' => '剧情',
    '3' => '悬疑',
    '4' => '喜剧',
    '5' => '爱情',
    '6' => '战争',
    '7' => '科幻',
    '8' => '灾难',
    '9' => '恐怖',
    '10' => '犯罪',
    '11' => array('动漫','动画'),
    '12' => '惊悚',
    '13' => '奇幻',
    '14' => '冒险',
	'26' => '纪录片'
);

$auto = isset($_REQUEST["auto"]) ? $_REQUEST["auto"] : die("QQ:1716001590");
if(!empty($auto) && $auto!="abcd"){die("Validation failure");}
date_default_timezone_set('PRC');

$filelog = __DIR__.'/autopost.log';
$fileLogMaxSize = 1024 ;
if (abs(@filesize($filelog)) < (int)$fileLogMaxSize) {
    @file_put_contents($filelog, date("Y-m-d H:i:s"). PHP_EOL, FILE_APPEND | LOCK_EX );
} else {
    @file_put_contents($filelog, date("Y-m-d H:i:s"). PHP_EOL, LOCK_EX );
}

$ret = $wpdb->query(" update ".$wpdb->prefix."auto_list set autoTime=now() where autoTime is null ");
if($ret)die;

$ret = $wpdb->get_results(" select term,url,autoTime,timediff(now(),autoTime) from ".$wpdb->prefix."auto_list where timediff(now(),autoTime)>'".$space_list."' order by autoTime limit 1 ");
if($ret){
    //dump($ret);
    $wpdb->query(" update " . $wpdb->prefix . "auto_list set autoTime=now() where url='" . $ret[0]->url . "' ");
    //var_dump($ret[0]->url);
    if(strpos($ret[0]->url,"torrentapi.org")>0){
        $url="https://torrentapi.org/pubapi_v2.php?app_id=MyRarBGApp&get_token=get_token";
        $content = get_fcontent($url,10);
        $results = json_decode($content);
        $url = $ret[0]->url."&token=".$results->token;
        if (stripos($url, "app_id=") <= 0) {
            $url .= "&app_id=MyRarBGApp";
        }
        //echo $url;
        //exit;
        //echo 'a';
        //$url = 'https://torrentapi.org/pubapi_v2.php?mode=list&category=42&min_seeders=30&ranked=0&sort=seeders&format=json_extended&limit=100&token=mzwgp8ahvq&app_id=MyRarBGApp';
        $content = get_fcontent($url,10);
         //echo $content;
         //exit;
        
        $results = json_decode($content);
        $results = $results->torrent_results;
        //dump($results);
        
        $db = new MySqli(constant("DB_HOST"), constant("DB_USER"), constant("DB_PASSWORD"), constant("DB_NAME"));
        !mysqli_connect_error() or die("连接数据库错误： " . mysqli_connect_error());
        mysqli_query($db, "set names utf8"); 
        $sql = " insert into " . $wpdb->prefix . "auto_movie (term,imdb,a) VALUES (?,?,?)";
        $stmt1 = $db->prepare($sql);
        $stmt1->bind_param("dss",$term,$imdb,$a);
        $sql = " update " . $wpdb->prefix . "auto_movie set a=?,a_upTime=? where id=?; ";
        $stmt2 = $db->prepare($sql);
        $stmt2->bind_param("ssd",$a,$a_upTime,$id);
        
        if($results){
            foreach($results as $key1=>$value1){
                // if($key1>10){exit;};
                if(empty($value1->episode_info->imdb)){continue;}
                if($value1->seeders<=$seeders){continue;}
                if($value1->leechers<$leechers){continue;}
                if($value1->size<($size_min*1024)*1024 or $value1->size>($size_max*1024)*1024){continue;}
                $value1->download = explode("&tr=",$value1->download)[0]."&tr=udp%3A%2F%2F%2Ftracker.coppersurfer.tk%3A6%3A6969&tr=udp%3A%2F%2F%2Ftracker.leechers-paradise.org%3A6%3A6969&tr=udp%3A%2F%2F%2Ftracker.opentrackr.org%3A1%3A1337%2Fannounce&tr=udp%3A%2F%2F%2Ftorrent.gresille.org%3A8%3A80%2Fannounce&tr=udp%3A%2F%2F%2F9.rarbg.me%3A2%3A2710%2Fannounce&tr=udp%3A%2F%2F%2Fp4p.arenabg.com%3A1%3A1337&tr=udp%3A%2F%2F%2Ftracker.internetwarriors.net%3A1%3A1337";

                $ret_movie = $wpdb->get_results(" select * from ".$wpdb->prefix."auto_movie where imdb='".$value1->episode_info->imdb."'; ");
                if($ret_movie){
                    foreach($ret_movie as $key2=>$value2){
                        // dump($value1);
                        // dump($ret_movie);
                        $data_movie_a = json_decode($value2->a);
                        $newData = true;
                        foreach($data_movie_a as $key3=>$value3){
                            // dump("采集".$value1->download);
                            // dump("保存".$value3->download);
                            if($value1->download==$value3->download){
                                $newData = false;
                                continue;
                            }
                        }
                        if($newData){
                            $dataTemp = array();
                            foreach($data_movie_a as $key4=>$value4){
                                $dataTemp[$key4]["title"] = $value4->title;
                                $dataTemp[$key4]["category"] = $value4->category;
                                $dataTemp[$key4]["download"] = $value4->download;
                                $dataTemp[$key4]["seeders"] = $value4->seeders;
                                $dataTemp[$key4]["leechers"] = $value4->leechers;
                                $dataTemp[$key4]["size"] = $value4->size;
                                $dataTemp[$key4]["pubdate"] = $value4->pubdate;
                            }                        
                            $key = count($dataTemp);
                            $id = $value2->id ;
                            $a_upTime = date("Y-m-d H:i:s");
                            $dataTemp[$key]["title"] = $value1->title;
                            $dataTemp[$key]["category"] = $value1->category;
                            $dataTemp[$key]["download"] = $value1->download;
                            $dataTemp[$key]["seeders"] = $value1->seeders;
                            $dataTemp[$key]["leechers"] = $value1->leechers;
                            $dataTemp[$key]["size"] = $value1->size;
                            $dataTemp[$key]["pubdate"] = $value1->pubdate;
                            //dump($dataTemp);
                            $a = json_encode($dataTemp,JSON_UNESCAPED_UNICODE);
                            $stmt2->execute();
                            echo "QQ:1716001590 => a更新 ".$value1->title."<br>";
                        }else{
                            echo "QQ:1716001590 => a地址重复不用更 imdb:".$value2->imdb."<br>";
                        }
                    }
                }else{
                    $term = $ret[0]->term ;
                    $imdb = $value1->episode_info->imdb ;
                    $data[0]["title"] = $value1->title;
                    $data[0]["category"] = $value1->category;
                    $data[0]["download"] = $value1->download;
                    $data[0]["seeders"] = $value1->seeders;
                    $data[0]["leechers"] = $value1->leechers;
                    $data[0]["size"] = $value1->size;
                    $data[0]["pubdate"] = $value1->pubdate;
                    $a = json_encode($data,JSON_UNESCAPED_UNICODE);
                    $stmt1->execute();
                    echo "QQ:1716001590 => a采集 ".$value1->title."<br>";
                }
            }
        }else{
            die('QQ:1716001590 => a获取列表数据失败 '.$url.'<br>');
        }
    }elseif(strpos($ret[0]->url,"baidu.com")>0){
        echo "222";
    }
    
}else{

    $ret_count = $wpdb->get_results(" select count(*)as iscount,(select count(*) from ".$wpdb->prefix."auto_movie where b_addTime is NULL or b_addTime='0000-00-00 00:00:00')as isnull from ".$wpdb->prefix."auto_movie ");
    if($ret_count[0]->iscount==$ret_count[0]->isnull){
        $ret = $wpdb->get_results(" select * from ".$wpdb->prefix."auto_movie where b_addTime is null or b_addTime='0000-00-00 00:00:00' order by rand() limit 1 ");
    }else{
        $ret = $wpdb->get_results(" select ".$wpdb->prefix."auto_movie.*,(select b_addTime from ".$wpdb->prefix."auto_movie order by b_addTime desc limit 1),timediff(now(),(select b_addTime from ".$wpdb->prefix."auto_movie order by b_addTime desc limit 1)) from ".$wpdb->prefix."auto_movie where timediff(now(),(select b_addTime from ".$wpdb->prefix."auto_movie order by b_addTime desc limit 1))>'".$space_post."' and (b_addTime is null or b_addTime='0000-00-00 00:00:00') order by rand() limit 1 ");
    }
    if($ret){
        $url = 'https://api.douban.com/v2/movie/imdb/'.$ret[0]->imdb;
        $content = get_fcontent($url,10);
        $results = json_decode($content);
        if($results->rating){
            $data["id"] = explode("/",$results->id)[4];
            $data["alt_title"] = $results->alt_title;
            $data["title"] = $results->title;
            $image = str_replace("/movie_poster_cover/ipst/","/movie_poster_cover/lpst/",$results->image);
            $image = str_replace("/view/photo/s_ratio_poster/public/","/view/movie_poster_cover/lpst/public/",$image);
            $data["image"] = $image ;
            $data["summary"] = $results->summary; 
            $data["rating"]["average"] = $results->rating->average;
            $data["rating"]["numRaters"] = $results->rating->numRaters;
            foreach((array)$results->author as $key=>$value){
                $data["author"][$key]["name"] = $results->author[$key]->name;
            }
            foreach((array)$results->attrs->website as $key=>$value){
                $data["attrs"]["website"][$key] = $results->attrs->website[$key];
            }
            foreach((array)$results->attrs->pubdate as $key=>$value){ 
                $data["attrs"]["pubdate"][$key] = $results->attrs->pubdate[$key];
            }
            foreach((array)$results->attrs->language as $key=>$value){
                $data["attrs"]["language"][$key] = $results->attrs->language[$key];
            }
            foreach((array)$results->attrs->country as $key=>$value){
                $data["attrs"]["country"][$key] = $results->attrs->country[$key];
            }
            foreach((array)$results->attrs->writer as $key=>$value){
                $data["attrs"]["writer"][$key] = $results->attrs->writer[$key];
            }
            foreach((array)$results->attrs->director as $key=>$value){
                $data["attrs"]["director"][$key] = $results->attrs->director[$key];
            }
            foreach((array)$results->attrs->cast as $key=>$value){
                $data["attrs"]["cast"][$key] = $results->attrs->cast[$key];
            }
            foreach((array)$results->attrs->year as $key=>$value){
                $data["attrs"]["year"][$key] = $results->attrs->year[$key];
            }
            foreach((array)$results->attrs->movie_type as $key=>$value){
                $data["attrs"]["movie_type"][$key] = $results->attrs->movie_type[$key];
            }
            foreach((array)$results->attrs->movie_duration as $key=>$value){
                $data["attrs"]["movie_duration"][$key] = $results->attrs->movie_duration[$key];
            }
            foreach((array)$results->tags as $key=>$value){
                $data["tags"][$key] = $value->name; 
            }
            //dump($data);
            
            $db = new MySqli(constant("DB_HOST"), constant("DB_USER"), constant("DB_PASSWORD"), constant("DB_NAME"));
            !mysqli_connect_error() or die("连接数据库错误： " . mysqli_connect_error());
            mysqli_query($db, "set names utf8"); 
            $sql = " update " . $wpdb->prefix . "auto_movie set b=?,b_addTime=? where id=?; ";
            $stmt1 = $db->prepare($sql);
            $stmt1->bind_param("ssd",$b,$b_addTime,$id);
            $b = json_encode($data,JSON_UNESCAPED_UNICODE);
            $b_addTime = date("Y-m-d H:i:s");
            $id = $ret[0]->id;
            $stmt1->execute();
            
            echo "QQ:1716001590 => b采集 ".$results->alt_title;
            echo make($ret[0]->id);
            exit;
        }

    }else{
        $ret = $wpdb->get_results(" select * from ".$wpdb->prefix."auto_movie where a is not null and b is not null and postid>0 and makeTime is not null and (a_upTime>makeTime or makeTime<'".$makeNode."') limit 1; ");
        if($ret){
            echo "QQ:1716001590 => c更新 ".$ret[0]->postid;
            echo make($ret[0]->id);
            exit;
        }
    }
}
exit;




















function make($id=0){
    global $wpdb;
    $db = new MySqli(constant("DB_HOST"), constant("DB_USER"), constant("DB_PASSWORD"), constant("DB_NAME"));
    !mysqli_connect_error() or die("连接数据库错误： " . mysqli_connect_error());
    mysqli_query($db, "set names utf8"); 
   
    $ret = $wpdb->get_results(" select * from ".$wpdb->prefix."auto_movie where a is not null and b is not null and id='".$id."'; ");
    if($ret){
        foreach($ret as $key=>$value){
            //dump($value);
            $cmd = is_null($value->makeTime) && $value->postid==0 ? "insert" : "update" ;
            $postid = $value->postid;
            $term = $value->term;
            $a = json_decode($value->a);
            $b = json_decode($value->b);
            
            $title_str = '[';
            if(!empty($b->alt_title)){$title_str .= $b->alt_title;}
            if(!empty($b->title)){
                if(!empty($b->alt_title)){$title_str .= '/';}
                $title_str .= $b->title;
            }
            $year = $b->attrs->year;
            if(count($year)){$title_str .= ']['.$year[0];}
            $country = $b->attrs->country;
            if(count($country)){$title_str .= ']['.$country[0];}
            $movie_type = $b->attrs->movie_type;
            if(count($movie_type)){$title_str .= ']['.$movie_type[0];}
            if(is_array($a)){
                $i_a = 0 ;
                foreach($a as $key=>$value){
                    if($i_a){$size_str .= '/';}
                    $size_str .= round((($value->size/1024)/1024)/1024,2) .'G' ;
                    $i_a ++ ;
                }
                //$title_str .= ']['.$size_str ;
            }
            $language = $b->attrs->language;
            if(count($language)){$title_str .= ']['.$language[0];}
            $title_str .= ']';
            //echo $title_str;
            // exit;
            
            $title = empty($b->alt_title) ? $title=$b->title : $title=$b->alt_title ;

            $confirm = 0 ;
            if(!empty($b->image)){
                $image_url = $b->image;
                $image_str = '<a href="'.$image_url.'" title="'.$title_str.'"><img class="aligncenter" src="'.$image_url.'" alt="'.$title_str.'" width="450"/></a>';
            }
            $average = $b->rating->average;
            if(!empty($average)){
                $average_str = "豆瓣评分：".$average."";
                $confirm ++ ;
            }
            $author = $b->author;
            if(is_array($author)){
                $author_str = "<br>导演：";
                foreach($author as $key=>$value){
                    $author_str .= $value->name . " / " ;
                }
                if(substr($author_str,-3)==" / "){$author_str = substr($author_str,0,-3);}
                $confirm ++ ;
            }
            $cast = $b->attrs->cast;
            if(is_array($cast)){
                $cast_str = "<br>演员：" ;
                foreach($cast as $key=>$value){
                    $cast_str .= $value . " / " ;
                }
                if(substr($cast_str,-3)==" / "){$cast_str = substr($cast_str,0,-3);}
                $confirm ++ ;
            }
            $pubdate = $b->attrs->pubdate;
            if(is_array($pubdate)){
                $pubdate_str = "<br>上映日期：";
                foreach($pubdate as $key=>$value){
                    $pubdate_str .= $value;
                    $confirm++;
                    break;
                }
                if(substr($pubdate_str,-3)==" / "){$pubdate_str = substr($pubdate_str,0,-3);}
            }
            $summary = $b->summary;
            if(!empty($summary)){
                $summary_str = $summary;
                $confirm++;
            }
            $download = $a;
            if(is_array($download)){
                foreach($download as $key=>$value){
                    if(!empty($download_str)){
                        $download_str .= '<div style="height: 10px;"></div>';
                    }
                    $download_suburl_str = '';
                    if(!empty($value->suburl)){
                        $download_suburl_str = '&nbsp;&nbsp;<a href="?download=suburl&id='.$ret[0]->id.'&title='.$value->title.'" target="_blank">字幕</a>';
                    }
                    $download_str .= '<h2>高清电影下载：<a href="?download=magnet&id='.$ret[0]->id.'&title='.$value->title.'" target="_blank">'.$value->title.'</a><strong>['.round((($value->size/1024)/1024)/1024,2) .'G]</strong>'.$download_suburl_str.'</h2>';
                }
            }
            $post_str = "$image_str
<h1>影片名：$title</h1>
$average_str
$author_str
$cast_str
$pubdate_str
<h2>剧情介绍：</h2>
　　$summary_str
$download_str";
            
            foreach($GLOBALS['term_arr'] as $key1=>$value1){
                if(is_array($value1)){
                    foreach($value1 as $key2=>$value2){
                        if(strpos($movie_type[0],$value2)>-1 || strpos($value2,$movie_type[0])>-1){
                            $term = $key1;
                            break;
                        }
                    }
                }else{
                    if(strpos($movie_type[0],$value1)>-1 || strpos($value1,$movie_type[0])>-1){
                        $term = $key1;
                        break;
                    }
                }
            }
//            dump($post_str);
//            echo sanitize_title($title);
//            exit;
            $db->autocommit(false); //开始事物
            if($cmd=="insert"){
                $sql = " insert into " . $wpdb->prefix . "posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";
                $stmt1 = $db->prepare($sql);
                $stmt1->bind_param("isssssssssssssssssssss", $post_author, $post_date, $post_date_gmt, $post_content, $post_title, $post_excerpt, $post_status, $comment_status, $ping_status, $post_password, $post_name, $to_ping, $pinged, $post_modified, $post_modified_gmt, $post_content_filtered, $post_parent, $guid, $menu_order, $post_type, $post_mime_type, $comment_count);
                $post_author = 1;
                $post_date = date('Y-m-d H:i:s');
                $post_date_gmt = date('Y-m-d H:i:s');
                $post_content = $post_str;
                $post_title = trim($title_str);
                $post_excerpt = '';
                $post_status = 'publish';
                $comment_status = 'open';
                $ping_status = 'open';
                $post_password = '';
//                require('PinYin_class.php');
//                $post_name = pinyin::utf8_to(trim($title));
                $post_name = sanitize_title($title_str);
                $to_ping = '';
                $pinged = '';
                $post_modified = date('Y-m-d H:i:s');
                $post_modified_gmt = date('Y-m-d H:i:s');
                $post_content_filtered = '';
                $post_parent = '0';
                $guid = '';
                $menu_order = '0';
                $post_type = 'post';
                $post_mime_type = '';
                $comment_count = '0';
                $stmt1->execute();
                $new_post_id = $db->insert_id;

                $sql = " insert into " . $wpdb->prefix . "term_relationships (object_id,term_taxonomy_id,term_order) VALUES (?,?,?) ";
                $stmt2 = $db->prepare($sql);
                $stmt2->bind_param("sss", $object_id, $term_taxonomy_id, $term_order);
                $object_id = $db->insert_id;
                $term_taxonomy_id = $term;
                $term_order = 0;
                $stmt2->execute();
                
                $sql = " update " . $wpdb->prefix . "auto_movie set postid=?,makeTime=? where id=?; ";
                $stmt3 = $db->prepare($sql);
                $stmt3->bind_param("dsd",$postid,$makeTime,$id);
                $postid = $new_post_id;
                $makeTime = date("Y-m-d H:i:s");
                $id = $ret[0]->id;
                $stmt3->execute();
                
                if ($stmt1->error || $stmt2->error || $stmt3->error) {
                    $db->rollback();    //回滚
                } else {
                    $db->commit();      //提交事物
                }
            }else{
                $sql = " update " . $wpdb->prefix . "posts set post_content=?,post_title=?,post_name=? where id=?; ";
                $stmt1 = $db->prepare($sql);
                $stmt1->bind_param("sssd",$post_content, $post_title,$post_name,$id);
                $post_content = $post_str;
                $post_title = trim($title_str);
                $post_name = sanitize_title($title_str);
                $id = $postid;
                $stmt1->execute();
                $sql = " update " . $wpdb->prefix . "auto_movie set makeTime=? where id=?; ";
                $stmt2 = $db->prepare($sql);
                $stmt2->bind_param("sd",$makeTime,$id);
                $makeTime = date("Y-m-d H:i:s");
                $id = $ret[0]->id;
                $stmt2->execute();
                if ($stmt1->error || $stmt2->error) {
                    $db->rollback();    //回滚
                } else {
                    $db->commit();      //提交事物
                }
            }
            $db->autocommit(true);  //不使用事物
        }
    }
}













function get_fcontent($url,  $timeout = 5 ) {
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

    $url = str_replace( "&amp;", "&", urldecode(trim($url)) );
    //$cookie = tempnam ("/tmp", "CURLCOOKIE");
    $cookie = "./cookie.txt";
    $ch = curl_init(); //模拟浏览器 在HTTP请求中包含一个"User-Agent: "头的字符串。
    //curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" ); //需要获取的URL地址，也可以在 curl_init()函数中设置。
    //curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1; rv:52.0) Gecko/20100101 Firefox/52.0" ); //需要获取的URL地址，也可以在 curl_init()函数中设置。
    curl_setopt( $ch, CURLOPT_URL, $url); //连接结束后保存cookie信息的文件。

    curl_setopt($ch, CURLOPT_COOKIE, "=tcc;q2bquVJn=qCBnZk87;aby=2;expla=1");
    //curl_setopt( $ch, CURLOPT_COOKIEJAR, $cookie); //启用时会将服务器服务器返回的"Location: "放在header中递归的返回给服务器，使用CURLOPT_MAXREDIRS可以限定递归返回的数量。
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true ); //HTTP请求头中"Accept-Encoding: "的值。支持的编码有"identity"，"deflate"和"gzip"。如果为空字符串""，请求头会发送所有支持的编码类型。在cURL 7.10中被加入。
    curl_setopt( $ch, CURLOPT_ENCODING, "" ); //将 curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true ); //当根据Location:重定向时，自动设置header中的Referer:信息。
    //curl_setopt( $ch, CURLOPT_AUTOREFERER, true ); //禁用后cURL将终止从服务端进行验证。使用CURLOPT_CAINFO选项设置证书使用CURLOPT_CAPATH选项设置证书目录 如果CURLOPT_SSL_VERIFYPEER(默认值为2)被启用，CURLOPT_SSL_VERIFYHOST需要被设置成TRUE否则设置为FALSE。自cURL 7.10开始默认为TRUE。从cURL 7.10开始默认绑定安装。
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls  //在发起连接前等待的时间，如果设置为0，则无限等待。
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout ); //设置cURL允许执行的最长毫秒数。
    curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout ); //指定最多的HTTP重定向的数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的。
    curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
    curl_setopt( $ch, CURLOPT_HTTPPROXYTUNNEL, true);
    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
    //curl_setopt( $ch, CURLOPT_PROXY, $url);
    $content = curl_exec( $ch );
    curl_close ( $ch );
    return $content;
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

/**
 * 功能: 生成随机字符串;
 * @param int $length 随机字串长度
 * @param int $type 随机字串类型
 * @return string
 */
function randStr($length = 6, $type = 0){
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

function curl($url,$method='GET'){
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($ch,CURLOPT_CUSTOMREQUEST,$method);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_TIMEOUT,30);
    $return['exec']=curl_exec($ch);
    $return['getinfo']=curl_getinfo($ch);
    curl_close($ch);
    //var_dump($return);
    return $return;
}