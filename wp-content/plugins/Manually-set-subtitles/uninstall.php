<?php
// 如果 uninstall 不是从 WordPress 调用，则退出
if( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();
// 从 options 表删除选项
// delete_option( 'boj_myplugin_options' );
// 删除其他额外的选项和自定义表
