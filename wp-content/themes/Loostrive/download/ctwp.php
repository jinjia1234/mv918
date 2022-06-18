<?php

class ctwp {
    public function __construct($params) {
        $this->params = $params;
        self::index();
    }

    //开始运行
    public function index() {
        $action = $this->params['request']['action'];
        if (!empty($action)) {
            switch ($action) {
                case 'ctwp_link':
                    print_r(self::get_file_share_link());
                    break;
            }
            exit;
        }
    }

    //获取本地附件信息
    public function get_local_file_info() {
        $postid = $this->params['request']['postid'];
        $title = $this->params['request']['title'];
        if (!empty($postid)) {
            $sql = " select am.id,am.imdb,am.a,am.postid,am.b,p.post_title,p.post_name from " . $this->params['db']->prefix . "auto_movie am join " . $this->params['db']->prefix . "posts p on am.postid=p.ID where postid='" . $this->params['request']['postid'] . "' limit 1 ";
            $retult = $this->params['db']->get_row($sql);
            if ($retult) {
                $data_movie_a = json_decode($retult->a);
                $data_movie_b = json_decode($retult->b);
                foreach ($data_movie_a as $key => $value) {
                    // var_dump($value->title);
                    // var_dump($title);
                    // var_dump($value->title == $title);
                    if ($value->title == $title) {
                        // var_dump('c');
                        if (preg_match('/btih:(.*?)&/', $value->download, $matches)) {
                            return [
                                //电影ID
                                'movie_id'     => $retult->id,
                                //电影标题
                                'movie_title'  => !empty($data_movie_b->alt_title) ? $data_movie_b->alt_title : $data_movie_b->title,
                                //电影标识
                                'movie_imdb'   => $retult->imdb,
                                //文章ID
                                'post_id'      => $retult->postid,
                                //文章标题
                                'post_title'   => $retult->post_title,
                                //文章URL标题
                                'post_name'   => $retult->post_name,
                                //磁力ID
                                'magnet_id'    => null,
                                //磁力标题
                                'magnet_title' => $title,
                                //磁力哈希值
                                'magnet_hash'  => $matches[1],
                            ];
                        }
                    }
                }
            }
        }
        return false;
    }

    //获取分享链接
    public function get_file_share_link() {
        $file_info = self::get_local_file_info();
        // var_dump(http_build_query($file_info));
        $result = self::curl('http://mv918.com/down/default/', [
            'body' => http_build_query($file_info),
        ]);
        // var_dump($result);
        if ($result) {
            return $result['exec'];
        }
        return false;
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

    /**
     * @param string $url 请求路径
     * @param array $option 数组参数 ['header','body','cookefile',cookie]
     * @return array
     */
    public function curl($url, $option = []) {
        try {
            $ch = curl_init();
            if (stripos($url, "https://") !== false) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSLVERSION, 1);
            }
            if (isset($option['header'])) {
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $option['header']);    //提交header一维数组
            }
            if (isset($option['body'])) {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $option['body']);      //提交body二维数组
            }
            if (isset($option['cookiefile'])) {
                curl_setopt($ch, CURLOPT_COOKIEFILE, $option['cookiefile']);
                curl_setopt($ch, CURLOPT_COOKIEJAR, $option['cookiefile']);     //保存cookie的绝对路径
            }
            if (isset($option['cookie'])) {
                curl_setopt($ch, CURLOPT_COOKIE, $option['cookie']);    //提交cookie字符串
            }
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);    //当遇到310跳转时,抓取跳转的页面
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HEADER, true);         //返回 response_header, 如果不为 true, 只会获得响应的正文
            curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);    //CURLINFO_HEADER_OUT选项可以拿到请求头信息
            $exec = curl_exec($ch);
            $getinfo = curl_getinfo($ch);
            $curlinfo_header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $return = array(
                'exec'    => substr($exec, $curlinfo_header_size),
                'getinfo' => $getinfo + [
                        'response_header' => substr($exec, 0, $curlinfo_header_size)
                    ],
                'error'   => curl_error($ch),
                'errno'   => curl_errno($ch),
            );
            curl_close($ch);
            return $return;
        } catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
        }
    }
}

new ctwp([
    'request' => $_REQUEST,
    'db'      => $wpdb,
]);
