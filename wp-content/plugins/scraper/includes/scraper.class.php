<?php
include_once 'scrapeer/scraper.php';

class scraper {
    public $id = null;
    public $config;

    public function __construct() {
        $this->config['minSeeders'] = defined('scraper_minSeeders') ? constant("scraper_minSeeders") : 1;
    }

    public function execute() {
        global $wpdb;
        $scraper = new scrapeer\scraper();
        if (empty($this->id)) {
            $result = $wpdb->get_row(" select m.*,(select post_status from " . $wpdb->prefix . "posts where ID=m.postid)post_status from " . $wpdb->prefix . "auto_movie as m where scraperTime is null order by rand(); ");
            if (!$result) {
                // 用户量小时，采用倒序检测
                $result = $wpdb->get_row(" select m.*,(select post_status from " . $wpdb->prefix . "posts where ID=m.postid)post_status from " . $wpdb->prefix . "auto_movie as m where timediff(now(),scraperTime)>'00:00:10' order by scraperTime; ");
                $wpdb->update($wpdb->prefix . "auto_movie", array('scraperTime' => date("Y-m-d H:i:s")), array('id' => $result->id));
                // 用户量多时，采用随机检测
                // $result = $wpdb->get_row(" select m.*,(select post_status from " . $wpdb->prefix . "posts where ID=m.postid)post_status from " . $wpdb->prefix . "auto_movie as m where timediff(now(),scraperTime)>'24:00:00' order by rand(); ");
            }
        } else {
            $result = $wpdb->get_row(" select m.*,(select post_status from " . $wpdb->prefix . "posts where ID=m.postid)post_status from " . $wpdb->prefix . "auto_movie as m where id='" . $this->id . "'; ");
        }

        if ($result) {
            ini_set('max_execution_time', 120);
            // makePost::dump(json_decode($result->b));
            if ($a = json_decode($result->a)) {
                $b = json_decode($result->b);
                $maxSeeders = 0;
                $result->fail = 0;
                foreach ($a as $key => $value) {
                    $data[$key] = $value;
                    if (preg_match('/magnet:\?xt=urn:btih:([a-z0-9]*?)&dn=([\s\S]*?)&/', $value->download, $magnet)) {
                        $info = $scraper->scrape($magnet[1], json_decode(constant("TRACKER_SCRAPE")));
                        if ($info) {
                            foreach ($info as $k => $v) {
                                $data[$key]->seeders = $v['seeders'];
                                $data[$key]->completed = $v['completed'];
                                $data[$key]->leechers = $v['leechers'];
                            }
                            $data_array = array(
                                'movieid'   => $result->id,
                                'hash'      => $magnet[1],
                                // 'title' => rawurldecode($magnet[2]),
                                'title'     => $value->title,
                                'size'      => $value->size,
                                'seeders'   => $data[$key]->seeders,
                                'completed' => $data[$key]->completed,
                                'leechers'  => $data[$key]->leechers,
                                'upTime'    => date("Y-m-d H:i:s"),
                                'display'   => 1,
                            );
                            $ret = $wpdb->get_row(" select * from `" . $wpdb->prefix . "auto_movie_magnet` where `movieid`='" . $result->id . "' and  `hash`='" . $magnet[1] . "'; ");
                            if ($ret) {
                                $wpdb->update($wpdb->prefix . "auto_movie_magnet", $data_array, array('movieid' => $result->id, 'hash' => $magnet[1]));
                            } else {
                                $wpdb->insert($wpdb->prefix . "auto_movie_magnet", $data_array);
                            }
                            $maxSeeders = $maxSeeders >= $data[$key]->seeders ? $maxSeeders : $data[$key]->seeders;
                        } else {
                            //makePost::dump($magnet[1]);
                            $result->fail++;
                        }
                    }

                }
                $ret = $wpdb->update($wpdb->prefix . "auto_movie", array('a' => json_encode($this->assoc_unique($data, 'title')), 'maxSeeders' => $maxSeeders, 'scraperTime' => date("Y-m-d H:i:s")), array('id' => $result->id));
                $makePost = new makePost();
                $postid = $makePost->execute($result->id);
                if ($maxSeeders >= $this->config['minSeeders']) {
                    $ret = $wpdb->update($wpdb->prefix . "posts", array('post_status' => 'publish'), array('id' => $postid));
                    $post_status = '<span style="color:#66cc00">显示</span>';
                } else {
                    $ret = $wpdb->update($wpdb->prefix . "posts", array('post_status' => 'draft'), array('id' => $postid));
                    $post_status = '<span style="color:red">草稿</span>';
                }
                $title = !empty($b->alt_title) ? $b->alt_title : $b->title;
                $return = "上次检测:{$result->scraperTime} <a href='/?p={$postid}' target='_blank'>{$title}</a> 的 " . count((array)$a) . "个磁力中{$result->fail}个失败 其中最大种子数{$result->maxSeeders}>{$maxSeeders}";
                if ($result->post_status == 'publish') {
                    $return .= ' 设置为<span style="color:#66cc00">显示</span>>' . $post_status;
                } else {
                    $return .= ' 设置为<span style="color:red">草稿</span>>' . $post_status;
                }
                echo $return;
                return $return;
            }
        }
    }

    public function draft() {
        global $wpdb;
        //将全部未检测或检测到种子数<指定值的文章修改为草稿
        $result = $wpdb->query(" update " . $wpdb->prefix . "posts set post_status='draft' where post_type='post' and post_status='publish' and id not in(select postid from " . $wpdb->prefix . "auto_movie where maxSeeders>=" . $this->config['minSeeders'] . " and id in(select movieid from " . $wpdb->prefix . "auto_movie_magnet where seeders=maxSeeders and size is not null )); ");
        return $result;
    }

    public function magnet($str) {
        return defined("TRACKER_MAGNET") ? explode("&tr=", $str)[0] . '&tr=' . implode('&tr=', array_map('urlencode', json_decode(constant("TRACKER_MAGNET")))) : $str;
    }

    /**
     * 二维数组去重
     * @param $arr 数组
     * @return $key 去重字段
     */
    public function assoc_unique($arr, $key) {
        $tmp_arr = array();
        $arr = (array)$arr;
        foreach ($arr as $k => $v) {
            //搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
            $v = (array)$v;
            if (in_array($v[$key], $tmp_arr)) {
                unset($arr[$k]);
            } else {
                $tmp_arr[] = $v[$key];
            }
        }
        //sort($arr); //sort函数对数组进行排序
        return $arr;
    }
}
