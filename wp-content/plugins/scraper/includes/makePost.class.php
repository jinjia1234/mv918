<?php

class makePost {
    public $term_arr = array(       //自动分类
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
        '11' => array('动漫', '动画'),
        '12' => '惊悚',
        '13' => '奇幻',
        '14' => '冒险',
        '26' => '纪录片'
    );
    public function execute($id=false) {
        if(!$id) return 'No ID';
        global $wpdb;
        $db = new MySqli(constant("DB_HOST"), constant("DB_USER"), constant("DB_PASSWORD"), constant("DB_NAME"));
        !mysqli_connect_error() or die("连接数据库错误： " . mysqli_connect_error());
        mysqli_query($db, "set names utf8");

        $result = $wpdb->get_row(" select * from " . $wpdb->prefix . "auto_movie where a is not null and b is not null and id='" . $id . "'; ");
        if ($result) {
            //$this->dump($result);
            $cmd = is_null($result->makeTime) && $result->postid == 0 ? "insert" : "update";
            $postid = $result->postid;
            $term = $result->term;
            $a = json_decode($result->a);
            $b = json_decode($result->b);

            $title_str = '[';
            if (!empty($b->alt_title)) {
                $title_str .= $b->alt_title;
            }
            if (!empty($b->title)) {
                if (!empty($b->alt_title)) {
                    $title_str .= '/';
                }
                $title_str .= $b->title;
            }
            $year = $b->attrs->year;
            if (!empty($year) && count($year)) {
                $title_str .= '][' . $year[0];
            }
            $country = $b->attrs->country;
            if (!empty($country) && count($country)) {
                $title_str .= '][' . $country[0];
            }
            $movie_type = $b->attrs->movie_type;
            if (!empty($movie_type) && count($movie_type)) {
                $title_str .= '][' . $movie_type[0];
            }
            if (is_array($a)) {
                $i_a = 0;
                $size_str = null;
                foreach ($a as $key => $value) {
                    if ($i_a) {
                        $size_str .= '/';
                    }
                    $size_str .= round((($value->size / 1024) / 1024) / 1024, 2) . 'G';
                    $i_a++;
                }
                //$title_str .= ']['.$size_str ;
            }
            $language = $b->attrs->language;
            if (!empty($language) && count($language)) {
                $title_str .= '][' . $language[0];
            }
            $title_str .= ']';
            //echo $title_str;
            // exit;

            $title = empty($b->alt_title) ? $title = $b->title : $title = $b->alt_title;

            $confirm = 0;
            if (!empty($b->image)) {
                $image_url = $b->image;
                $image_str = '<a href="' . $image_url . '" title="' . $title_str . '"><img class="aligncenter" src="'. $image_url . '" alt="' . $title_str . '" width="450" /></a>';
            }
            $average = $b->rating->average;
            if (!empty($average)) {
                $average_str = "豆瓣评分：" . $average . "";
                $confirm++;
            }
            $author = $b->author;
            if (is_array($author)) {
                $author_str = "<br>导演：";
                foreach ($author as $key => $value) {
                    $author_str .= $value->name . " / ";
                }
                if (substr($author_str, -3) == " / ") {
                    $author_str = substr($author_str, 0, -3);
                }
                $confirm++;
            }
            $cast = $b->attrs->cast;
            if (is_array($cast)) {
                $cast_str = "<br>演员：";
                foreach ($cast as $key => $value) {
                    $cast_str .= $value . " / ";
                }
                if (substr($cast_str, -3) == " / ") {
                    $cast_str = substr($cast_str, 0, -3);
                }
                $confirm++;
            }
            $pubdate = $b->attrs->pubdate;
            if (is_array($pubdate)) {
                $pubdate_str = "<br>上映日期：";
                foreach ($pubdate as $key => $value) {
                    $pubdate_str .= $value;
                    $confirm++;
                    break;
                }
                if (substr($pubdate_str, -3) == " / ") {
                    $pubdate_str = substr($pubdate_str, 0, -3);
                }
            }
            $summary = $b->summary;
            if (!empty($summary)) {
                $summary_str = $summary;
                $confirm++;
            }
            $download = $a;
            //$this->dump($download);
            if ($download) {
                $temp_array_1 = [];
                $temp_array_2 = [];
                foreach ($download as $key => $value) {
                    if(stripos($value->title,'2160p')){
                        $temp_array_1[] = $value;
                    }else{
                        $temp_array_2[] = $value;
                    }
                }
                $download = array_merge($temp_array_1,$temp_array_2);

                $download_str = null;
                $magnet = $wpdb->get_row(" select * from " . $wpdb->prefix . "auto_movie_magnet where movieid={$result->id} order by seeders desc ; ");
                $scraper = new scraper();
                //判断最大种子数 都 小于限制，那就显示全部磁力吧
                if ($magnet->seeders < $scraper->config['minSeeders']) {
                    foreach ($download as $key => $value) {
                        $download_suburl_str = '';
                        if (!empty($value->suburl)) {
                            $download_suburl_str = '&nbsp;&nbsp;<a href="?download=suburl&id=' . $result->id . '&title=' . $value->title . '" target="_blank">字幕</a>';
                        }
                        $download_str .= '<h2>高清电影下载：<a href="?download=magnet&id=' . $result->id . '&title=' . $value->title . '" target="_blank">' . $value->title . '</a>[' . round((($value->size / 1024) / 1024) / 1024, 2) . 'G][种子:' . $value->seeders . ']' . $download_suburl_str . '</h2><div style="height: 10px;"></div>';
                    }
                } else {
                    foreach ($download as $key => $value) {
                        $download_suburl_str = '';
                        if (!empty($value->suburl)) {
                            $download_suburl_str = '&nbsp;&nbsp;<a href="?download=suburl&id=' . $result->id . '&title=' . $value->title . '" target="_blank">字幕</a>';
                        }
                        $magnet = $wpdb->get_row(" select * from " . $wpdb->prefix . "auto_movie_magnet where  movieid=$result->id and title='" . $value->title . "' ; ");
                        if ($magnet) {
                            if (class_exists('scraper')) {
                                $scraper = new scraper();
                                if($magnet->seeders >= $scraper->config['minSeeders']){
                                    $download_str .= '<h2>高清电影下载：<a href="?download=magnet&id=' . $result->id . '&title=' . $value->title . '" target="_blank">' . $value->title . '</a>[' . round((($value->size / 1024) / 1024) / 1024, 2) . 'G][种子:' . $value->seeders . ']' . $download_suburl_str . '</h2><div style="height: 10px;"></div>';
                                }else{
                                    //var_dump($magnet->seeders);
                                }
                            }
                        }
                    }
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
            //var_dump($post_str);
            foreach ($this->term_arr as $key1 => $value1) {
                if (is_array($value1)) {
                    foreach ($value1 as $key2 => $value2) {
                        if (strpos($movie_type[0], $value2) > -1 || strpos($value2, $movie_type[0]) > -1) {
                            $term = $key1;
                            break;
                        }
                    }
                } else {
                    if (strpos($movie_type[0], $value1) > -1 || strpos($value1, $movie_type[0]) > -1) {
                        $term = $key1;
                        break;
                    }
                }
            }
            //            dump($post_str);
            //            echo sanitize_title($title);
            //            exit;
            $db->autocommit(false); //开始事物
            if ($cmd == "insert") {
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
                $stmt3->bind_param("dsd", $postid, $makeTime, $id);
                $postid = $new_post_id;
                $makeTime = date("Y-m-d H:i:s");
                $id = $result->id;
                $stmt3->execute();

                if ($stmt1->error || $stmt2->error || $stmt3->error) {
                    $db->rollback();    //回滚
                } else {
                    $db->commit();      //提交事物
                }
            } else {
                $sql = " update " . $wpdb->prefix . "posts set post_content=?,post_title=?,post_name=? where id=?; ";
                $stmt1 = $db->prepare($sql);
                $stmt1->bind_param("sssd", $post_content, $post_title, $post_name, $id);
                $post_content = $post_str;
                $post_title = trim($title_str);
                $post_name = sanitize_title($title_str);
                $id = $postid;
                $stmt1->execute();
                $sql = " update " . $wpdb->prefix . "auto_movie set makeTime=? where id=?; ";
                $stmt2 = $db->prepare($sql);
                $stmt2->bind_param("sd", $makeTime, $id);
                $makeTime = date("Y-m-d H:i:s");
                $id = $result->id;
                $stmt2->execute();
                if ($stmt1->error || $stmt2->error) {
                    $db->rollback();    //回滚
                } else {
                    $db->commit();      //提交事物
                }
            }

            $db->autocommit(true);  //不使用事物
            return $postid>0 ? $postid : $new_post_id;
        }
    }

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
}