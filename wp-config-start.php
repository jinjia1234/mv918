<?php
/**
 * 配置开始前的兼容处理
 * 在wp-config.php头部引入：
 * include_once "wp-config-start.php";
 */


/** 禁止访问 xmlrpc.php */
if (stripos($_SERVER['REQUEST_URI'], 'xmlrpc.php') !== false) {
    $protocol = $_SERVER['SERVER_PROTOCOL'] ?? '';
    if (!in_array($protocol, ['HTTP/1.1', 'HTTP/2', 'HTTP/2.0', 'HTTP/3'], true)) {
        $protocol = 'HTTP/1.0';
    }
    header("$protocol 403 Forbidden", true, 403);
    die;
}
/** 启用SSL防止循环重定向 */
if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) {
    $_SERVER['HTTPS'] = 'on';
    define('FORCE_SSL_LOGIN', true);
    define('FORCE_SSL_ADMIN', true);
}
if (!function_exists('is_secure')) {
    /** 是否通过加密的 */
    function is_secure() {
        if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') return true;
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') return true;
        if (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') return true;
        if (isset($_SERVER['SERVER_PORT']) && intval($_SERVER['SERVER_PORT']) === 443) return true;
        return false;
    }
}

if (isset($_SERVER['HTTP_HOST'])) {
    /** 登录 多域名支持 */
    $domain_name = explode(':', $_SERVER['HTTP_HOST']);
    $domain_name = array_shift($domain_name);
    define('COOKIEPATH', '/');
    define('COOKIE_DOMAIN', $domain_name);
    define('COOKIEHASH', md5($domain_name));
    /** 设置 多域名支持 */
    $needle = '/wp-admin';
    $separator = stripos($_SERVER['SCRIPT_NAME'], $needle) > -1 ? $needle : '/';
    $rootUrl = 'http' . (is_secure() ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . implode('/', explode($separator, $_SERVER['SCRIPT_NAME'], -1));
    // 配合 子目录主题 开始
    $sub_dir_theme = [
        'm'      => 'mobile',
        'mobile' => 'mobile',
    ];  // 定义子目录与主题的对应关系
    define('SUB_DIR_THEME', json_encode($sub_dir_theme, 320));
    $paths = explode('/', trim(parse_url($_SERVER['REQUEST_URI'])['path'], '/'));
    foreach ($sub_dir_theme as $key => $value) {
        if (in_array($key, $paths)) {
            define('WP_SITEURL', "{$rootUrl}/{$key}");
            define('WP_HOME', "{$rootUrl}/{$key}");
            define('WP_CONTENT_URL', "{$rootUrl}/wp-content");
            break;
        }
    }
    // 配合 子目录主题 结束
    if (!defined('WP_SITEURL')) define('WP_SITEURL', $rootUrl);
    if (!defined('WP_HOME')) define('WP_HOME', $rootUrl);
}

/** 禁用自动保存 */
define('AUTOSAVE_INTERVAL', false);
/** 设置自动保存间隔/秒 */
// define('AUTOSAVE_INTERVAL', 120);
/** 禁用文章修订 */
define('WP_POST_REVISIONS', false);
/** 设置修订版本最多允许几个 */
// define('WP_POST_REVISIONS', 3);
/** 当前页面执行的sql查询保存到一个数组中 $wpdb->queries */
define('SAVEQUERIES', true);
