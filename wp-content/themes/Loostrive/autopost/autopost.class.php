<?php

class autopost {
    public function __construct() {
        switch ($_REQUEST['mode']) {                        //模式
            case 'make':                                    //生成post
                if ((int)$_REQUEST['id'] > 0) {
                    $makePost = new makePost();
                    $result = $makePost->execute($_REQUEST['id']);
                    var_dump($result);
                }
                exit;
            case 'scraper':                                 //采集movie
                if ((int)$_REQUEST['id'] > 0) {
                    $scraper = new scraper();
                    $scraper->id = $_REQUEST['id'];
                    $scraper->execute();
                }
                exit;
            default:
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
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $return['exec'] = curl_exec($ch);
        $return['getinfo'] = curl_getinfo($ch);
        curl_close($ch);
        //var_dump($return);
        return $return['exec'];
    }
}