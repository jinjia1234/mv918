<?php

class getPirate {
    public $config = array(
        'limit' => null,
    );

    public function __construct() {
        $this->config['limit'] = $this->config['limit'] && !empty($this->config['limit']) && (int)$this->config['limit'] > 0 ? $this->config['limit'] : 2;
        // $results = db("scraper_website")->where(['type' => 1])->select();
        // $this->config['urls'] = array_column($results, 'url');
    }

    public function test() {
        dump($this->get_list('https://pirateproxy.bet/top/201', true));
    }

    public function get_list11() {
        $page = $this->get_content('https://thehiddenbay.com/top/201');
        // dump($page);
        if ($page) {
            foreach ($page as $key => $value) {
                $table = db("movie")->alias("m");
                $table->join("movie_magnet mm", "m.id=mm.mid", "left");
                $result = $table->where(['btih' => $value['btih']])->select();
                dump($table->getLastSql());
                dump($result);

            }
        }
        exit;
        for ($i = 0; $i < count($links[0]); $i++) {
            preg_match('/\/torrent\/(.*?)\//', $links[1][$i], $id);
            preg_match('/<.*?>(.*?)<.*?>/', $links[6][$i], $author);
            $details = $this->get_details($links[1][$i]);
            $lists[] = array(
                'id'         => $id[1],
                'title'      => $links[2][$i],
                'detail_url' => $links[1][$i],
                'author'     => $author[1],
                'size'       => str_replace(['&nbsp;', 'MiB', 'GiB', 'KiB'], ['', 'MB', 'GB', 'KB'], $links[5][$i]),
                'magnet'     => $links[3][$i],
                'seeders'    => (int)$links[7][$i],
                'leechers'   => (int)$links[8][$i],
                'added'      => str_replace(['&nbsp;'], ['-'], $links[4][$i]),
                'imdb'       => $details && isset($details['imdb']) ? $details['imdb'] : null,
            );
            //dump($lists);
            //exit;
        }
    }

    public function get_content11($url) {
        set_time_limit(3000);
        $page = $this->curl($url);
        preg_match_all('/<div class="detName">.*?<a href="(.*?)" class="detLink" title=".*?">(.*?)<\/a>[\n|\s]*?<\/div>[\n|\s]*?<a href="(.*?)" title="Download this torrent using magnet">[\s\S]*?<font class="detDesc">Uploaded (.*?), Size (.*?), ULed by (.*?)<\/font>[\n|\s]*?<\/td>[\n|\s]*?<td align="right">(.*?)<\/td>[\n|\s]*?<td align="right">(.*?)<\/td>/', $page, $links);
        // dump($links);
        // exit;
        $lists = [];
        for ($i = 0; $i < count($links[0]); $i++) {
            preg_match('/\/torrent\/(.*?)\//', $links[1][$i], $id);
            preg_match('/<.*?>(.*?)<.*?>/', $links[6][$i], $author);
            preg_match('/magnet:\?xt=urn:btih:(.*?)&dn=/', $links[3][$i], $btih);
            $lists[] = array(
                'id'         => $id[1],
                'title'      => $links[2][$i],
                'detail_url' => $links[1][$i],
                'author'     => $author[1],
                'size'       => str_replace(['&nbsp;', 'MiB', 'GiB', 'KiB'], ['', 'MB', 'GB', 'KB'], $links[5][$i]),
                'magnet'     => $links[3][$i],
                'btih'       => $btih[1],
                'seeders'    => (int)$links[7][$i],
                'leechers'   => (int)$links[8][$i],
                'added'      => str_replace(['&nbsp;'], ['-'], $links[4][$i]),
            );
        }
        return $lists;
    }

    public function get($keyword = null, $details = false) {
        $results = db("scraper_website")->where(['typeid' => 1])->select();
        $this->config['urls'] = array_column($results, 'url');
        if (count($this->config['urls']) > 0) {
            foreach ($this->config['urls'] as $value) {
                $keyword = '/' == substr($keyword, 0, 1) ? $keyword : '/' . $keyword;
                $url = implode('/', array_slice(explode('/', $value), 0, 3)) . $keyword;
                dump($url);
                $results = $this->get_list($url, $details);
                if ($results) return $results;
            }
        }
    }

    public function get_list($url = '', $details = false) {
        set_time_limit(5000);
        $page = $this->curl($url);
        // dump($page);
        if (-1 == strstr($page, 'detName')) return false;
        preg_match_all('/<div class="detName">.*?<a href="(.*?)" class="detLink" title=".*?">(.*?)<\/a>[\n|\s]*?<\/div>[\n|\s]*?<a href="(.*?)" title="Download this torrent using magnet">[\s\S]*?<font class="detDesc">[\S]*? (.*?), [\S]*? (.*?),(.*?)<\/font>[\n|\s]*?<\/td>[\n|\s]*?<td align="right">(.*?)<\/td>[\n|\s]*?<td align="right">(.*?)<\/td>/', $page, $links);
        // dump($links);
        // exit;
        $detail = [];
        $limit = $this->config['limit'] && !empty($this->config['limit']) && (int)$this->config['limit'] > 0 ? $this->config['limit'] : count($links[0]);
        $limit = $limit < count($links[0]) ? $limit : count($links[0]);
        for ($i = 0; $i < $limit; $i++) {
            preg_match('/\/torrent\/(.*?)\//', $links[1][$i], $id);
            preg_match('/<.*?>(.*?)<.*?>/', $links[6][$i], $author);
            preg_match('/magnet:\?xt=urn:btih:(.*?)&dn=/', $links[3][$i], $btih);
            $details_url = strstr($links[1][$i], 'http') ? $links[1][$i] : implode('/', array_slice(explode('/', $url), 0, 3)) . $links[1][$i];
            if ($details) {
                $result = $this->get_details($details_url);
                if ($result) {
                    $detail[$i] = array(
                            'imdb' => $result['imdb'],
                        );
                }else{
                    continue;
                }
            }
            $lists[$i] = array(
                'id'         => $id[1],
                'title'      => $links[2][$i],
                'detail_url' => $details_url,
                'author'     => $author[1],
                'size'       => str_replace(['&nbsp;', 'MiB', 'GiB', 'KiB'], ['', 'MB', 'GB', 'KB'], $links[5][$i]),
                'magnet'     => $links[3][$i],
                'btih'       => $btih[1],
                'seeders'    => (int)$links[7][$i],
                'leechers'   => (int)$links[8][$i],
                'update'     => str_replace(['&nbsp;'], [' '], $links[4][$i]),
            ) + $detail[$i];
            // dump($lists);
            // exit;
        }
        return $lists;
    }

    public function get_details($url) {
        $page = $this->curl($url);
        //dump($page);
        if ($page) {
            preg_match_all('/<dt>Info:<\/dt>[\n|\s\S]*?imdb\.com\/title\/(.*?)\/"/', $page, $results);
            //dump($results);
            if (isset($results[0][0])) {
                return array(
                    'imdb' => $results[1][0],
                );
            } else return false;
        } else return false;
    }

    public function curl($url, $method = 'GET') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko',
        ));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); //当遇到310跳转时,抓取跳转的页面
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $return = array(
            'exec'    => curl_exec($ch),
            'getinfo' => curl_getinfo($ch),
        );
        curl_close($ch);
        //var_dump($return);
        return $return['exec'];
    }
}