<?php
/**
 * Plugin Name: Pirate Search
 * Plugin URI: http://tt3p.com
 * Description: 搜索海盗湾
 * Version: 1.0.0
 * Author: QQ1716001590
 * Author URI: http://blog.tt3p.com
 * License: GPLv2
 */

include_once('includes/ArrayAndObjectAccess.php');
include_once('includes/index.php');

$config = new ArrayAndObjectAccess([
    'page' => basename(__FILE__, '.php'),
]);


add_action('admin_menu', function () {
    global $config;
    add_menu_page('搜索海盗湾', '海盗湾', 'administrator', $config['page'], function () {
        $wp_pirate = new wp_pirate();
        $wp_pirate->wp_search();
    }, 'dashicons-search', 4);
});

add_action('plugins_loaded', function () {
    global $config;
    $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : null;
    if(!empty($page) && $config['page']==$page){
        switch ($_REQUEST["action"]){
            case "ajax_detail":
                $wp_pirate = new wp_pirate();
                $wp_pirate->ajax_detail($_REQUEST["url"]);
                exit;
            case "form_submit":
                $wp_pirate = new wp_pirate();
                $wp_pirate->from_submit();
                exit;
            default:
        }
    }
});

