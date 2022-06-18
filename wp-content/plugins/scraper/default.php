<?php

$result =  new scraper();
//$result->id = 2346 ;          //检测指定id
echo $result->execute();
$ret = $result->draft();
if($ret){
    echo '<br>将 '.$ret.' 条未检测或检测到种子数小于 '.$result->config['minSeeders'].' 的文章修改为草稿';
}







//$scraper = new scrapeer\scraper();
//$result = $wpdb->get_row(" select m.* from " . $wpdb->prefix . "auto_movie as m where scraperTime is null order by rand(); ");
//if (!$result) {
//    $result = $wpdb->get_row(" select m.* from " . $wpdb->prefix . "auto_movie as m where timediff(now(),scraperTime)>'00:00:10' order by scraperTime; ");
//}
//if ($result) {
//    if ($a = json_decode($result->a)) {
//        $result->maxSeeders = 0;
//        $result->fail = 0;
//        foreach ($a as $key => $value) {
//            $data[$key] = $value;
//            if (preg_match('/magnet:\?xt=urn:btih:([a-z0-9]*?)&dn=([\s\S]*?)&/', $value->download, $magnet)) {
//                $info = $scraper->scrape($magnet[1], 'udp://tracker.opentrackr.org:1337/announce');
//                if ($info) {
//                    foreach ($info as $k => $v) {
//                        $data[$key]->seeders = $v['seeders'];
//                        $data[$key]->completed = $v['completed'];
//                        $data[$key]->leechers = $v['leechers'];
//                    }
//                    $data_array = array(
//                        'movieid' => $result->id,
//                        'hash' => $magnet[1],
//                        'title' => $magnet[2],
//                        'size' => $value->size,
//                        'seeders' => $data[$key]->seeders,
//                        'completed' => $data[$key]->completed,
//                        'leechers' => $data[$key]->leechers,
//                        'upTime' => date("Y-m-d H:i:s"),
//                        'display' => 1,
//                    );
//                    $ret = $wpdb->get_row(" select * from `" . $wpdb->prefix . "auto_movie_magnet` where `movieid`='" . $result->id . "' and  `hash`='" . $magnet[1] . "'; ");
//                    if ($ret) {
//                        $wpdb->update($wpdb->prefix . "auto_movie_magnet", $data_array, array('movieid' => $result->id, 'hash' => $magnet[1]));
//                    } else {
//                        $wpdb->insert($wpdb->prefix . "auto_movie_magnet", $data_array);
//                    }
//                    $result->maxSeeders = $result->maxSeeders >= $data[$key]->seeders ? $result->maxSeeders : $data[$key]->seeders;
//                } else {
//                    //dump($magnet[1]);
//                    $result->fail++;
//                }
//            }
//        }
//
//        $ret = $wpdb->update($wpdb->prefix . "auto_movie", array('a' => json_encode($data), 'maxSeeders' => $result->maxSeeders, 'scraperTime' => date("Y-m-d H:i:s")), array('id' => $result->id));
//        //dump($ret);
//
//
//        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//        if ($b = json_decode($result->b)) {
//            $title_str = '[';
//            if (!empty($b->alt_title)) {
//                $title_str .= $b->alt_title;
//            }
//            if (!empty($b->title)) {
//                if (!empty($b->alt_title)) {
//                    $title_str .= '/';
//                }
//                $title_str .= $b->title;
//            }
//            $year = $b->attrs->year;
//            if (count($year)) {
//                $title_str .= '][' . $year[0];
//            }
//            $country = $b->attrs->country;
//            if (count($country)) {
//                $title_str .= '][' . $country[0];
//            }
//            $movie_type = $b->attrs->movie_type;
//            if (count($movie_type)) {
//                $title_str .= '][' . $movie_type[0];
//            }
//            if (is_array($a)) {
//                $i_a = 0;
//                foreach ($a as $key => $value) {
//                    if ($i_a) {
//                        $size_str .= '/';
//                    }
//                    $size_str .= round((($value->size / 1024) / 1024) / 1024, 2) . 'G';
//                    $i_a++;
//                }
//                //$title_str .= ']['.$size_str ;
//            }
//            $language = $b->attrs->language;
//            if (count($language)) {
//                $title_str .= '][' . $language[0];
//            }
//            $title_str .= ']';
//            //echo $title_str;
//            // exit;
//
//            $title = empty($b->alt_title) ? $title = $b->title : $title = $b->alt_title;
//
//            if (!empty($b->image)) {
//                $image_url = $b->image;
//                $image_str = '<a href="' . $image_url . '" title="' . $title_str . '"><img class="aligncenter" src="' . $image_url . '" alt="' . $title_str . '" width="450"/></a>';
//            }
//            $average = $b->rating->average;
//            if (!empty($average)) {
//                $average_str = "豆瓣评分：" . $average . "";
//            }
//            $author = $b->author;
//            if (is_array($author)) {
//                $author_str = "<br>导演：";
//                foreach ($author as $key => $value) {
//                    $author_str .= $value->name . " / ";
//                }
//                if (substr($author_str, -3) == " / ") {
//                    $author_str = substr($author_str, 0, -3);
//                }
//            }
//            $cast = $b->attrs->cast;
//            if (is_array($cast)) {
//                $cast_str = "<br>演员：";
//                foreach ($cast as $key => $value) {
//                    $cast_str .= $value . " / ";
//                }
//                if (substr($cast_str, -3) == " / ") {
//                    $cast_str = substr($cast_str, 0, -3);
//                }
//            }
//            $pubdate = $b->attrs->pubdate;
//            if (is_array($pubdate)) {
//                $pubdate_str = "<br>上映日期：";
//                foreach ($pubdate as $key => $value) {
//                    $pubdate_str .= $value;
//                    break;
//                }
//                if (substr($pubdate_str, -3) == " / ") {
//                    $pubdate_str = substr($pubdate_str, 0, -3);
//                }
//            }
//            $summary = $b->summary;
//            if (!empty($summary)) {
//                $summary_str = $summary;
//            }
//        }
//        $download = $a;
//        if (is_array($download)) {
//            foreach ($download as $key => $value) {
//                if (!empty($download_str)) {
//                    $download_str .= '<div style="height: 10px;"></div>';
//                }
//                $download_suburl_str = '';
//                if (!empty($value->suburl)) {
//                    $download_suburl_str = '&nbsp;&nbsp;<a href="?download=suburl&id=' . $result->id . '&title=' . $value->title . '" target="_blank">字幕</a>';
//                }
//                $magnet = $wpdb->get_row(" select * from " . $wpdb->prefix . "auto_movie_magnet where movieid=$result->id and title='" . $value->title . "' ; ");
//                if ($magnet) {
//                    $download_str .= '<h2>高清电影下载：<a href="?download=magnet&id=' . $result->id . '&title=' . $value->title . '" target="_blank">' . $value->title . '</a>[' . round((($value->size / 1024) / 1024) / 1024, 2) . 'G][种子:' . $value->seeders . ']' . $download_suburl_str . '</h2>';
//                }
//            }
//        }
//        $post_str = "$image_str
//    <h1>影片名：$title</h1>
//    $average_str
//    $author_str
//    $cast_str
//    $pubdate_str
//    <h2>剧情介绍：</h2>
//    　　$summary_str
//    $download_str";
//        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//        if ($result->maxSeeders >= $config['minSeeders']) {
//            $ret = $wpdb->update($wpdb->prefix . "posts", array('post_status' => 'publish', 'post_content' => $post_str), array('id' => $result->postid));
//            $post_status = '<span style="color:#66cc00">显示</span>';
//        } else {
//            $ret = $wpdb->update($wpdb->prefix . "posts", array('post_status' => 'draft'), array('id' => $result->postid));
//            $post_status = '<span style="color:red">草稿</span>';
//        }
//        echo '检测 <a href="?p=' . $result->postid . '" target="_blank">' . $title . '</a> 的 ' . count($a) . '个磁力中 ' . $result->fail . '个失败 其中最大种子数为 ' . $result->maxSeeders . ' 已设置为 ' . $post_status;
//
//        //将全部未检测或检测到种子数<指定值的文章修改为草稿
//        //$ret = $wpdb->query(" update ".$wpdb->prefix ."posts set post_status='draft' where post_type='post' and post_status='publish' and id not in(select postid from ".$wpdb->prefix ."auto_movie where maxSeeders>=".$config['minSeeders']." and id in(select movieid from ".$wpdb->prefix ."auto_movie_magnet where seeders=maxSeeders and size is not null )); ");
//    }
//}
