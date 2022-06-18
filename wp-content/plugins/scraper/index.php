<?php
/**
 * Plugin Name: 获取Torrent链接信息
 * Plugin URI: http://gamecf.cn
 * Description: 获取Torrent链接信息
 * Version: 1.0.0
 * Author: QQ1716001590
 * Author URI: http://blog.gamecf.cn
 * License: GPLv2
 */


/** 设置scraper插件过滤最小种子数 */
define('scraper_minSeeders', 20);


/** set tracker scrape start */
define('TRACKER_SCRAPE', json_encode(array(
    // 'udp://9.rarbg.me:2710/announce',
    'udp://tracker.opentrackr.org:1337/announce',
    // 'udp://bt.xxx-tracker.com:2710/announce',
    // 'udp://tracker.leechers-paradise.org:6969/announce',
    // 'http://explodie.org:6969/announce',
)));
/** set tracker scrape end */






























/** set tracker magnet start */
define('TRACKER_MAGNET', json_encode(array(
    'udp://tracker.coppersurfer.tk:6969',
    'udp://tracker.leechers-paradise.org:6969',
    'udp://tracker.opentrackr.org:1337/announce',
    'udp://torrent.gresille.org:80/announce',
    'udp://9.rarbg.me:2710/announce',
)));
/** set tracker magnet end */


































include_once 'includes/makePost.class.php';
include_once 'includes/scraper.class.php';


add_action('plugins_loaded', function () {
    global $wpdb;
    $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";
    $action = isset($_REQUEST["action"]) ? $_REQUEST["action"] : "";
    header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
    header("Pragma: no-cache"); // HTTP 1.0.
    header("Expires: 0"); // Proxies.
    header("Content-Type: text/html;charset=utf-8");
    if (date_default_timezone_get() != "1Asia/Shanghai") date_default_timezone_set("Asia/Shanghai");
    if (!empty($page) && $page == "scraper" && !empty($action)) {
        include_once('default.php');
        exit;
    }
});






