<?php

class index extends base {
    public $config = [
        //循环采集列表间隔时间
        'spacing_list'    => '8:00:01',
        //采集内容间隔时间
        'spacing_post'    => '00:00:01',
        //此时间之前重新生成wp_post
        'make_time_node'  => '2017-10-11 11:21:00',
        //过滤种子连接数 大于 50
        'filter_seeders'  => '10',
        //过滤种子下载数 大于 3
        'filter_leechers' => '3',
        //过滤种子文件体积范围[最小,最大] 单位M
        'filter_size'     => [700, 99096],
        //日志文件路径
        'log_path'        => 'log.txt',
    ];
    public $params;

    public function __construct($data) {
        if ($_REQUEST && count($_REQUEST)) {
            foreach ($_REQUEST as $key => $value) {
                $this->params[$key] = $value;
            }
        }
        //引入wpdb数据操作类
        $this->db['wp'] = $data['wpdb'];
        //引入mysqli原生类
        $this->db['mysqli'] = new MySqli(constant("DB_HOST"), constant("DB_USER"), constant("DB_PASSWORD"), constant("DB_NAME"));
        !mysqli_connect_error() or die("连接数据库错误： " . mysqli_connect_error());
        mysqli_query($this->db['mysqli'], "set names utf8");
        //引入海盗湾操作类
        $this->class['pirate'] = $data['pirate'];
        //引入流程控制
        $this->initialize();
    }

    //流程控制
    public function initialize() {
        //判断auto_list采集
        $auto_list = $this->db['wp']->get_row(" select id,term,url,autoTime,timediff(now(),autoTime)timediff from " . $this->db['wp']->prefix . "auto_list where timediff(now(),autoTime)>'" . $this->config['spacing_list'] . "' order by autoTime limit 1 ");
        if ($auto_list) {
            //判断采集网站
            if (stripos($auto_list->url, "torrentapi.org") > 0) {
                //采集torrentapi.org列表
                $this->torrentapi_list($auto_list);
            } else {
                //采集海盗湾列表
                $this->HaiDaoWai_list($auto_list);
            }
        } else {
            //判断auto_movie采集
            $auto_movie = $this->db['wp']->get_row(" select count(*)as iscount,(select count(*) from " . $this->db['wp']->prefix . "auto_movie where b_addTime is NULL or b_addTime='0000-00-00 00:00:00')as isnull from " . $this->db['wp']->prefix . "auto_movie ");
            if ($auto_movie->iscount == $auto_movie->isnull) {
                $auto_movie = $this->db['wp']->get_row(" select * from " . $this->db['wp']->prefix . "auto_movie where imdb is not null and imdb<>'' and (b_addTime is null or b_addTime='0000-00-00 00:00:00') order by rand() limit 1 ");
            } elseif (isset($this->params['auto_movie_id']) && $this->params['auto_movie_id']) {
                $sql = " select {$this->db['wp']->prefix}auto_movie.* ";
                $sql .= " from {$this->db['wp']->prefix}auto_movie ";
                $sql .= " where imdb is not null and ";
                $sql .= " imdb<>'' and ";
                $sql .= " id='{$this->params['auto_movie_id']}' ";
                $sql .= " limit 1 ; ";
                $auto_movie = $this->db['wp']->get_row($sql);
            } else {
                $sql = " select {$this->db['wp']->prefix}auto_movie.* ";
                $sql .= " from {$this->db['wp']->prefix}auto_movie ";
                $sql .= " where imdb is not null and ";
                $sql .= " imdb<>'' and ";
                $sql .= " b like '{\"id\":null%' ";
                $sql .= " limit 1 ; ";
                $auto_movie = $this->db['wp']->get_row($sql);
                if (!$auto_movie) {
                    $sql = " select {$this->db['wp']->prefix}auto_movie.*, ";
                    $sql .= " (select b_addTime from {$this->db['wp']->prefix}auto_movie order by b_addTime desc limit 1), ";
                    $sql .= " timediff(now(),(select b_addTime from {$this->db['wp']->prefix}auto_movie order by b_addTime desc limit 1)) ";
                    $sql .= " from {$this->db['wp']->prefix}auto_movie ";
                    $sql .= " where imdb is not null and ";
                    $sql .= " imdb<>'' and ";
                    $sql .= " timediff(now(),(select b_addTime from {$this->db['wp']->prefix}auto_movie order by b_addTime desc limit 1))>'{$this->config['spacing_post']}' and ";
                    $sql .= " (b_addTime is null or b_addTime='0000-00-00 00:00:00') ";
                    $sql .= " order by rand() ";
                    $sql .= " limit 1 ; ";
                    $auto_movie = $this->db['wp']->get_row($sql);
                }
            }

            if ($auto_movie) {
                //采集豆瓣内容填充auto_movie表b字段
                // $this->DouBan_detailed($auto_movie); //在2020年过期的api所有注释掉
                $this->DouBan_search_api_update($auto_movie);
                if (class_exists('scraper')) {
                    $result = new scraper();
                    $result->id = $auto_movie->id;
                    $result->execute();
                    $this->logged("采集豆瓣后一并检测了磁力 > id:{$result->id}");
                } else {
                    $this->logged("采集豆瓣成功，但未检测磁力，请启用scraper插件");
                }
            } else {
                $h = date('H');
                // if ($h > 10) die('stop');
                //判断auto_movie更新
                $auto_movie = $this->db['wp']->get_row(" select * from " . $this->db['wp']->prefix . "auto_movie where a is not null and b is not null and postid>0 and makeTime is not null and (a_upTime>makeTime or makeTime<'" . $this->config['make_time_node'] . "') and timediff(now(),makeTime)>'05:00:00' order by rand() limit 1; ");
                if ($auto_movie) {
                    //生成wp_post更新
                    if (class_exists('scraper')) {
                        $result = new scraper();
                        $result->id = $auto_movie->id;
                        //检测指定postid
                        if (!empty($_GET['postid'])) {
                            $row = $this->db['wp']->get_row(" select * from " . $this->db['wp']->prefix . "auto_movie where a is not null and b is not null and postid>0 and makeTime is not null and postid=" . $_GET['postid']);
                            $result->id = $row->id;
                        }
                        $movie_id = $result->id;
                        $result = $result->execute();
                        $this->logged("检测磁力链接 > id:{$movie_id} > {$result}");
                    } else {
                        echo '请启用scraper插件';
                    }
                } else {
                    //检测磁力链接
                    if (class_exists('scraper')) {
                        $result = new scraper();
                        //检测指定postid
                        if (!empty($_GET['postid'])) {
                            $row = $this->db['wp']->get_row(" select * from " . $this->db['wp']->prefix . "auto_movie where a is not null and b is not null and postid>0 and makeTime is not null and postid=" . $_GET['postid']);
                            $result->id = $row->id;
                        }
                        $result = $result->execute();
                        $this->logged("倒序检测磁力链接 > {$result}");
                    } else {
                        echo '请启用scraper插件';
                    }
                }
            }
        }
    }

    //采集数据列表初始化
    public function init_auto_list() {
        $ret = $this->db['wp']->query(" update " . $this->db['wp']->prefix . "auto_list set autoTime=now() where autoTime is null ");
    }

    //采集torrentapi.org列表
    public function torrentapi_list($auto_list) {
        //列表循环采集逻辑更新
        $this->db['wp']->query(" update " . $this->db['wp']->prefix . "auto_list set autoTime=now() where url='" . $auto_list->url . "' ");
        //拼接token准备采集网址
        $url = $auto_list->url . '&token=' . $this->torrentapi_token();
        if (stripos($url, "app_id=") <= 0) $url .= '&app_id=MyRarBGApp';
        //列表地址写入日志
        $this->logged("采集列表信息 > id:{$auto_list->id}，入库term:{$auto_list->term}，上次执行时间:{$auto_list->autoTime}，与上次间隔时间:{$auto_list->timediff}，url:{$url}");
        $result = $this->torrentapi_curl($url);
        if ($result['exec'] && $results = json_decode($result['exec'])) {
            $results = $results->torrent_results;
            if (is_array($results) && count($results) > 0) {
                foreach ($results as $key => $value) {
                    //过滤循环10次以上跳出
                    // if ($key > 10) break;
                    //过滤没有imdb编号数据
                    if (empty($value->episode_info->imdb)) {
                        $this->logged("列表{$key} > 过滤没有imdb编号 > title:{$value->title}");
                        continue;
                    }
                    //过滤种子连接数
                    if ($value->seeders < $this->config['filter_seeders']) {
                        $this->logged("列表{$key} > 过滤种子连接数低于{$this->config['filter_seeders']} > seeders:{$value->seeders}，title:{$value->title}");
                        continue;
                    }
                    //过滤种子下载数
                    if ($value->leechers < $this->config['filter_leechers']) {
                        $this->logged("列表{$key} > 过滤种子下载数低于{$this->config['filter_leechers']} > leechers:{$value->leechers}，title:{$value->title}");
                        continue;
                    }
                    //过滤种子文件体积
                    if ($value->size < ($this->config['filter_size'][0] * 1024) * 1024 or $value->size > ($this->config['filter_size'][1] * 1024) * 1024) {
                        $this->logged("列表{$key} > 过滤种子文件体积范围[{$this->config['filter_size'][0]},{$this->config['filter_size'][1]}] > size:{$value->size}，title:{$value->title}");
                        continue;
                    }
                    //组建新的电影数据
                    $data = [
                        'title'    => $value->title,
                        'category' => $value->category,
                        'download' => $value->download,
                        'seeders'  => $value->seeders,
                        'leechers' => $value->leechers,
                        'size'     => $value->size,
                        'pubdate'  => $value->pubdate,
                    ];
                    //判断数据库是否存在此imdb
                    $result = $this->db['wp']->get_row(" select * from " . $this->db['wp']->prefix . "auto_movie where imdb='" . $value->episode_info->imdb . "'; ");
                    if ($result) {
                        //数据存在则更新
                        $auto_movie_a = json_decode($result->a, true);
                        $push = true;
                        foreach ($auto_movie_a as $k => $v) {
                            if (substr($value->download, 0, 60) == substr($v['download'], 0, 60)) {
                                $push = false;
                                continue;
                            }
                        }
                        if ($push) {
                            array_push($auto_movie_a, $data);
                            $db = $this->db['mysqli']->prepare(" update " . $this->db['wp']->prefix . "auto_movie set a=?,a_upTime=? where id=?; ");
                            $a = json_encode($auto_movie_a, 320);
                            $a_upTime = date("Y-m-d H:i:s");
                            $id = $result->id;
                            $db->bind_param("ssd", $a, $a_upTime, $id);
                            $res = $db->execute();
                            $this->logged("列表{$key} > 采集更新 > id:{$result->id}，imdb:{$result->imdb}, title:{$value->title}，result:{$res}，error:{$db->error}");
                        } else {
                            $this->logged("列表{$key} > 过滤重复 > id:{$result->id}，imdb:{$result->imdb}, title:{$value->title}");
                        }
                    } else {
                        //数据不存在则创建
                        $db = $this->db['mysqli']->prepare(" insert into " . $this->db['wp']->prefix . "auto_movie (term,imdb,a) VALUES (?,?,?); ");
                        $term = $auto_list->term;
                        $imdb = $value->episode_info->imdb;
                        $a = json_encode([$data], 320);
                        $db->bind_param("dss", $term, $imdb, $a);
                        $result = $db->execute();
                        $this->logged("列表{$key} > 采集添加 > imdb:{$value->episode_info->imdb}, title:{$value->title}，result:{$result}，error:{$db->error}");
                    }
                }
            } else {
                $this->logged("列表 > 获取列表数据失败 > url:{$url}");
            }
        } else {
            $this->logged("列表 > 获取数据失败 > url:{$url}");
        }
    }

    //获取torrentapi.org的token
    public function torrentapi_token() {
        $token_file = __DIR__ . DIRECTORY_SEPARATOR . __FUNCTION__ . ".json";         //缓存文件名;
        fopen($token_file, "a");       //如果文件不存在则尝试创建
        $data = (object)json_decode(file_get_contents($token_file));
        if ($data->expire_time <= time() or !$data->expire_time) {
            $url = "https://torrentapi.org/pubapi_v2.php?app_id=MyRarBGApp&get_token=get_token";
            $token = $this->torrentapi_curl($url);
            $token = json_decode($token['exec']);
            if ($token->token) {
                $data->token = $token->token;
                $data->expire_time = time() + 900;
                $fp = fopen($token_file, "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        }
        return $data->token;
    }

    public function torrentapi_curl($url, $option = []) {
        $user_agent = false;
        if (isset($option['header'])) {
            foreach ($option['header'] as $key => $value) {
                if (stripos($value, 'User-Agent') > 0) {
                    $user_agent = true;
                    continue;
                }
            }
        } else {
            $option['header'] = [];
        }
        if (!$user_agent) array_push($option['header'], 'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko');
        $result = $this->curl($url, $option);
        return $result;
    }

    //采集海盗湾列表
    public function HaiDaoWai_list($auto_list) {
        //列表循环采集逻辑更新
        $this->db['wp']->query(" update " . $this->db['wp']->prefix . "auto_list set autoTime=now() where url='" . $auto_list->url . "' ");
        //列表地址写入日志
        $this->logged("采集列表信息 > id:{$auto_list->id}，入库term:{$auto_list->term}，上次执行时间:{$auto_list->autoTime}，与上次间隔时间:{$auto_list->timediff}，url:{$auto_list->url}");
        //开始采集海盗
        $this->class['pirate']->config['limit'] = 30000;
        $result = $this->class['pirate']->get_list($auto_list->url, true);
        if (is_array($result) && count($result) > 0) {
            foreach ($result as $key => $value) {
                $value = (object)$value;
                //过滤没有imdb编号数据
                if (empty($value->imdb)) {
                    $this->logged("列表{$key} > 过滤没有imdb编号 > title:{$value->title}");
                    continue;
                }
                //过滤种子连接数
                if ($value->seeders < $this->config['filter_seeders']) {
                    $this->logged("列表{$key} > 过滤种子连接数低于{$this->config['filter_seeders']} > seeders:{$value->seeders}，title:{$value->title}");
                    continue;
                }
                //过滤种子下载数
                if ($value->leechers < $this->config['filter_leechers']) {
                    $this->logged("列表{$key} > 过滤种子下载数低于{$this->config['filter_leechers']} > leechers:{$value->leechers}，title:{$value->title}");
                    continue;
                }
                //过滤种子文件体积
                if (!empty($this->config['filter_size'][0]) && !empty($this->config['filter_size'][1]) && !empty($value->size) && preg_match('/(\d+\.\d{0,2})(MB|GB|TB|PB|EB|ZB|YB)?/', $value->size, $sizes)) {
                    $size = $sizes[1];
                    for ($i = 1; $i <= array_search($sizes[2], ['MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']); $i++) $size = $size * 1024;
                    if ($size < $this->config['filter_size'][0] or $size > $this->config['filter_size'][1]) {
                        $this->logged("列表{$key} > 过滤种子文件体积范围[{$this->config['filter_size'][0]},{$this->config['filter_size'][1]}] > size:{$size}，title:{$value->title}");
                        continue;
                    }
                }
                //过滤豆瓣里没有imdb的数据
                if (!$this->filter_douban_imdb($value->imdb)) {
                    $this->logged("列表{$key} > 过滤豆瓣里没有imdb的编号 > imdb:{$value->imdb}，title:{$value->title}");
                    continue;
                }
                //组建新的电影数据
                $data = [
                    'title'    => $value->title,
                    'download' => $value->magnet,
                    'seeders'  => $value->seeders,
                    'leechers' => $value->leechers,
                    'size'     => round(($size * 1204) * 1024),
                    'pubdate'  => $value->update,
                ];
                //判断数据库是否存在此imdb
                $result = $this->db['wp']->get_row(" select * from " . $this->db['wp']->prefix . "auto_movie where imdb='" . $value->imdb . "'; ");
                if ($result) {
                    //数据存在则更新
                    $auto_movie_a = json_decode($result->a, true);
                    $push = true;
                    foreach ($auto_movie_a as $k => $v) {
                        if (substr($value->magnet, 0, 60) == substr($v['download'], 0, 60)) {
                            $push = false;
                            continue;
                        }
                    }
                    if ($push) {
                        array_push($auto_movie_a, $data);
                        $db = $this->db['mysqli']->prepare(" update " . $this->db['wp']->prefix . "auto_movie set a=?,a_upTime=? where id=?; ");
                        $a = json_encode($auto_movie_a, 320);
                        $a_upTime = date("Y-m-d H:i:s");
                        $id = $result->id;
                        $db->bind_param("ssd", $a, $a_upTime, $id);
                        $res = $db->execute();
                        $this->logged("列表{$key} > 采集更新 > id:{$result->id}，imdb:{$result->imdb}, title:{$value->title}，result:{$res}，error:{$db->error}");
                    } else {
                        $this->logged("列表{$key} > 过滤重复 > id:{$result->id}，imdb:{$result->imdb}, title:{$value->title}");
                    }
                } else {
                    //数据不存在则创建
                    $db = $this->db['mysqli']->prepare(" insert into " . $this->db['wp']->prefix . "auto_movie (term,imdb,a) VALUES (?,?,?); ");
                    $term = $auto_list->term;
                    $imdb = $value->imdb;
                    $a = json_encode([$data], 320);
                    $db->bind_param("dss", $term, $imdb, $a);
                    $result = $db->execute();
                    $this->logged("列表{$key} > 采集添加 > imdb:{$value->imdb}, title:{$value->title}，result:{$result}，error:{$db->error}");
                }
            }
        } else {
            $this->logged("列表 > 获取列表数据失败 > url:{$auto_list->url}");
        }
    }


    /**
     * @param $data
     * @return array|bool
     * 过滤豆瓣里没有 imdb 数据
     */
    public function filter_douban_imdb($data) {
        if (is_array($data)) {
            $newdata = array();
            foreach ($data as $key => $value) {
                if (strlen($value['imdb']) == 9) {
                    $result = $this->curl('https://api.douban.com/v2/movie/imdb/' . $value['imdb'] . '?apikey=0df993c66c0c636e29ecbb5344252a4a');
                    $result = json_decode($result);
                    if (!$result->code == 5000) {
                        $newdata[] = $value;
                    }
                }
            }
            return $newdata;
        } else {
            if (strlen($data) == 9) {
                $result = curl('https://api.douban.com/v2/movie/imdb/' . $data . '?apikey=0df993c66c0c636e29ecbb5344252a4a');
                $result = json_decode($result);
                return $result->code == 5000 ? false : true;
            } else {
                return false;
            }
        }
    }

    //采集豆瓣api(该接口在2020年已失效)
    public function DouBan_detailed($auto_movie) {
        $url = 'https://api.douban.com/v2/movie/imdb/' . $auto_movie->imdb . '?apikey=0df993c66c0c636e29ecbb5344252a4a';
        $url = 'http://frodo.douban.com/v2/movie/imdb/' . $auto_movie->imdb . '?apikey=054022eaeae0b00e0fc068c0c0a2102a';
        $result = $this->curl($url);
        if ($result['exec'] && $result = json_decode($result['exec'])) {
            //判断豆瓣是否有imdb,没有就从数据库删除
            if ($result->code == 5000) {
                $result_del = $this->db['wp']->query(" delete from " . $this->db['wp']->prefix . "auto_movie where id='" . $auto_movie->id . "'; ");
                if ($result_del) {
                    return $this->logged("豆瓣没有发现imdb:{$auto_movie->imdb} 删除id:{{$auto_movie->id}}记录 成功");
                } else {
                    return $this->logged("豆瓣没有发现imdb:{$auto_movie->imdb} 删除id:{{$auto_movie->id}}记录 失败");
                }
            } elseif ($result->rating) {
                //组建b字段数据
                $data["id"] = explode("/", $result->id)[4];
                $data["alt_title"] = $result->alt_title;
                $data["title"] = $result->title;
                $image = str_replace("/movie_poster_cover/ipst/", "/movie_poster_cover/lpst/", $result->image);
                $image = str_replace("/view/photo/s_ratio_poster/public/", "/view/movie_poster_cover/lpst/public/", $image);
                $data["image"] = $image;
                $data["summary"] = $result->summary;
                $data["rating"]["average"] = $result->rating->average;
                $data["rating"]["numRaters"] = $result->rating->numRaters;
                foreach ((array)$result->author as $key => $value) {
                    $data["author"][$key]["name"] = $result->author[$key]->name;
                }
                foreach ((array)$result->attrs->website as $key => $value) {
                    $data["attrs"]["website"][$key] = $result->attrs->website[$key];
                }
                foreach ((array)$result->attrs->pubdate as $key => $value) {
                    $data["attrs"]["pubdate"][$key] = $result->attrs->pubdate[$key];
                }
                foreach ((array)$result->attrs->language as $key => $value) {
                    $data["attrs"]["language"][$key] = $result->attrs->language[$key];
                }
                foreach ((array)$result->attrs->country as $key => $value) {
                    $data["attrs"]["country"][$key] = $result->attrs->country[$key];
                }
                foreach ((array)$result->attrs->writer as $key => $value) {
                    $data["attrs"]["writer"][$key] = $result->attrs->writer[$key];
                }
                foreach ((array)$result->attrs->director as $key => $value) {
                    $data["attrs"]["director"][$key] = $result->attrs->director[$key];
                }
                foreach ((array)$result->attrs->cast as $key => $value) {
                    $data["attrs"]["cast"][$key] = $result->attrs->cast[$key];
                }
                foreach ((array)$result->attrs->year as $key => $value) {
                    $data["attrs"]["year"][$key] = $result->attrs->year[$key];
                }
                foreach ((array)$result->attrs->movie_type as $key => $value) {
                    $data["attrs"]["movie_type"][$key] = $result->attrs->movie_type[$key];
                }
                foreach ((array)$result->attrs->movie_duration as $key => $value) {
                    $data["attrs"]["movie_duration"][$key] = $result->attrs->movie_duration[$key];
                }
                foreach ((array)$result->tags as $key => $value) {
                    $data["tags"][$key] = $value->name;
                }
                //b字段写入数据库
                $db = $this->db['mysqli']->prepare(" update " . $this->db['wp']->prefix . "auto_movie set b=?,b_addTime=? where id=?; ");
                $b = json_encode($data, 320);
                $b_addTime = date("Y-m-d H:i:s");
                $id = $auto_movie->id;
                $db->bind_param("ssd", $b, $b_addTime, $id);
                $db->execute();
                $this->logged("采集豆瓣 > id:{$auto_movie->id}，imdb:{$auto_movie->imdb}，alt_title:{$result->alt_title}，title:{$result->title}");
            }
        } else {
            $this->logged("获取豆瓣数据失败 > url:{$url}");
        }
    }

    //采用豆瓣电影搜索页面查找imdb号，获取豆瓣电影ID,然后通过豆瓣电影api获取数据并入库
    public function DouBan_search_api_update($auto_movie) {
        $url = "https://search.douban.com/movie/subject_search?search_text={$auto_movie->imdb}&cat=1002";
        // $url = "https://search.douban.com/movie/subject_search?search_text=tt0054215&cat=1002";
        $result = $this->curl($url);
        if ($result['exec']) {
            if (preg_match("/window.__DATA__ = \"(.*?)\"/is", $result['exec'], $matches)) {
                if ($matches[1]) {
                    //$result = $this->curl("http://123.56.40.226:31803", ['body' => $matches[1]]);
                    $result = $this->curl("http://39.98.109.164:8081", ['body' => $matches[1]]);
                    if ($result['exec']) {
                        $result = iconv('GBK', 'UTF-8', $result['exec']);
                        $result = json_decode($result, true);
                        //判断豆瓣是否搜索到imdb，没有就从数据库中删除
                        if (count($result['payload']['items']) == 0) {
                            $result_del = $this->db['wp']->query(" delete from " . $this->db['wp']->prefix . "auto_movie where id='" . $auto_movie->id . "'; ");
                            if ($result_del) {
                                return $this->logged("豆瓣没有发现imdb:{$auto_movie->imdb} 删除id:{{$auto_movie->id}}记录 成功");
                            } else {
                                return $this->logged("豆瓣没有发现imdb:{$auto_movie->imdb} 删除id:{{$auto_movie->id}}记录 失败");
                            }
                        } else {
                            /*
                            // 20210321 豆瓣接口已失效
                            $douban_id = $result['payload']['items'][0]['id'];
                            $url = "https://frodo.douban.com/api/v2/movie/{$douban_id}?apiKey=054022eaeae0b00e0fc068c0c0a2102a";
                            $result = $this->curl($url, ['header' => ['User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.87 Safari/537.36']]);
                            $result = json_decode($result['exec']);
                            //组建b字段数据
                            $data["id"] = $result->id;
                            $data["alt_title"] = implode('/',$result->aka);
                            $data["title"] = $result->original_title;
                            $image = str_replace("view/photo/m_ratio_poster/public", "view/photo/l_ratio_poster/public", $result->pic->large);
                            $data["image"] = $image;
                            $data["summary"] = $result->intro;
                            $data["rating"]["average"] = $result->rating->value;
                            $data["rating"]["numRaters"] = $result->rating->count;
                            foreach ((array)$result->directors as $key => $value) {
                                $data["author"][$key]["name"] = $result->directors[$key]->name;
                            }
                            foreach ((array)$result->attrs->website as $key => $value) {
                                $data["attrs"]["website"][$key] = $result->attrs->website[$key];
                            }
                            foreach ((array)$result->pubdate as $key => $value) {
                                $data["attrs"]["pubdate"][$key] = $result->pubdate[$key];
                            }
                            foreach ((array)$result->languages as $key => $value) {
                                $data["attrs"]["language"][$key] = $result->languages[$key];
                            }
                            foreach ((array)$result->countries as $key => $value) {
                                $data["attrs"]["country"][$key] = $result->countries[$key];
                            }
                            foreach ((array)$result->attrs->writer as $key => $value) {
                                $data["attrs"]["writer"][$key] = $result->attrs->writer[$key];
                            }
                            foreach ((array)$result->actors as $key => $value) {
                                $data["attrs"]["director"][$key] = $result->actors[$key]->name;
                            }
                            foreach ((array)$result->attrs->cast as $key => $value) {
                                $data["attrs"]["cast"][$key] = $result->attrs->cast[$key];
                            }
                            foreach ((array)$result->year as $key => $value) {
                                $data["attrs"]["year"][$key] = $value;
                            }
                            foreach ((array)$result->genres as $key => $value) {
                                $data["attrs"]["movie_type"][$key] = $result->genres[$key];
                            }
                            foreach ((array)$result->durations as $key => $value) {
                                $data["attrs"]["movie_duration"][$key] = $result->durations[$key];
                            }
                            foreach ((array)$result->tags as $key => $value) {
                                $data["tags"][$key] = $value->name;
                            }
                            */
                            //组建b字段数据
                            $result = $result['payload']['items'][0];
                            $data["id"] = $result['id'];
                            $data["alt_title"] = $result['title'];
                            $image = str_replace(["view/photo/m_ratio_poster/public","view/photo/s_ratio_poster/public"], "view/photo/l_ratio_poster/public", $result['cover_url']);
                            $data["image"] = $image;
                            $data["summary"] = "{$result['abstract']} {$result['abstract']}";
                            $data["rating"]["average"] = $result['rating']['value'];
                            $data["rating"]["numRaters"] = $result['rating']['count'];
                            //b字段写入数据库
                            $db = $this->db['mysqli']->prepare(" update " . $this->db['wp']->prefix . "auto_movie set b=?,b_addTime=? where id=?; ");
                            $b = json_encode($data, 320);
                            $b_addTime = date("Y-m-d H:i:s");
                            $id = $auto_movie->id;
                            $db->bind_param("ssd", $b, $b_addTime, $id);
                            $db->execute();
                            $this->logged("采集豆瓣 > id:{$auto_movie->id}，imdb:{$auto_movie->imdb}，alt_title:{$data["alt_title"]}，title:{$result->title}");
                        }
                    }
                }
            }
        }
    }

}
