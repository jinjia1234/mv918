<?php get_header(); ?>
<div class="container">
    <?php if (get_option('strive_gg') == 'Display') { ?>
        <div class="subsidiary row box">
            <div class="bulletin fourfifth">
                <span class="sixth">站点公告：</span>
                <marquee class="fivesixth" direction=left onmouseout=start(); onmouseover=stop(); scrollAmount=2 scrollDelay=15;>
                    <?php echo get_option('strive_announce'); ?>
                </marquee>
            </div>
            <div class="bdshare_small fifth">
                <?php if (get_option('strive_bdshare') == 'Display') { ?>
                    <!-- Baidu Button BEGIN -->
                    <div class="bdsharebuttonbox">
                        <a href="#" class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a>
                        <a href="#" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a>
                        <a href="#" class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博"></a>
                        <a href="#" class="bds_renren" data-cmd="renren" title="分享到人人网"></a>
                        <a href="#" class="bds_weixin" data-cmd="weixin" title="分享到微信"></a>
                        <a href="#" class="bds_huaban" data-cmd="huaban" title="分享到花瓣"></a>
                        <a href="#" class="bds_more" data-cmd="more"></a>
                    </div>
                    <!-- Baidu Button END -->
                <?php } ?>
            </div>
        </div>
    <?php } else {
        echo '<div class="row"></div>';
    } ?>
    <?php if(1==2): ?>
    <div class="poster row" style="display: none">
        <table width="100%" border="1" cellpadding="5" cellspacing="5">
            <tr>
                <?php
                $sql = " select p.* from " . $wpdb->prefix . "auto_movie m inner join " . $wpdb->prefix . "posts p on m.postid=p.ID where p.post_type='post' and post_status='publish' order by m.a_upTime desc limit 8; ";
                $results = $wpdb->get_results($sql);
                //var_dump($results);
                if ($results) {
                    global $post;
                    foreach ($results as $post) {
                        setup_postdata($post);
                        ?>
                        <td width="">
                            <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php echo post_thumbnail_img('100%',200); ?>
                        </td>
                        <?php
                    }
                    //重置 setup_postdata()的反函数
                    wp_reset_postdata();
                } ?>
            </tr>
        </table>
    </div>
    <?php endif; ?>
    <?php if (get_option('strive_slidebar') == 'Display') { ?>
        <?php get_sidebar(); ?>
        <?php if ($_REQUEST['index'] == "home") {
            if (get_option('strive_slides') == 'Display' && $post == $posts[0] && !is_paged()) { ?>
                <?php include('includes/slides.php'); ?>
                <?php {
                    echo '';
                } ?>
            <?php }
        }
    } ?>
    <?php if ($_REQUEST['index'] == "home") { ?>
        <div class="mainleft">
            <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('首页幻灯区域')) :; endif; ?>
            <ul id="post_container" class="masonry clearfix">
                <?php $limit = get_option('posts_per_page');
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1; ?>
                <?php if (get_option('sticky_posts')) {
                    query_posts(array('post__in' => get_option('sticky_posts'), 'ignore_sticky_posts' => 1, 'paged' => $paged)) ?>
                    <?php include('includes/list_post.php');
                } ?>
                <?php query_posts(array('cat' => get_option('strive_leiid'), 'post__not_in' => get_option('sticky_posts'), 'paged' => $paged)); ?>
                <?php include('includes/list_post.php'); ?>
            </ul>
            <div class="clear"></div>
            <div class="navigation container"><?php pagination(5); ?></div>
        </div>
    <?php } else {
        switch (strtolower($_REQUEST['order'])) {
            case 'seeders':
                $sql_order = 'm.maxSeeders desc';
                break;
            case 'size':
                $sql_order = 'size desc';
                break;
            default:
                $sql_order = 'p.ID desc';
        }
        switch (strtolower($_REQUEST['index'])){
            case '4k':
                $sql_where = " m.id in(select movieid from " . $wpdb->prefix . "auto_movie_magnet where movieid=m.id and title like '%2160p%' and seeders>='".constant("scraper_minSeeders")."' ) and ";
                $page_title = '高清4K';
                break;
            case 'thematic':
                $sql_where = " thematic='".$_REQUEST['id']."' and ";
                $page_title = '专题影片';
                break;
            default:
                $sql_where = '';
                $page_title = '最新片源';
        }
        ?>
        <style>
            table th { white-space: nowrap; padding: 2px 5px;}
        </style>
        <div class="mainleft">
            <div class="article_container row  box">
                <h1 class="page_title">最新片源</h1>
                <div class="context">
                    <table>
                        <tr>
                            <th>电影名称</th>
                            <th>大小</th>
                            <th><a href="<?php echo home_url('?'.http_build_query(['index'=>$_GET['index'],'id'=>$_GET['id'],'order'=>'seeders']));?>">种子数</a></th>
                            <th><a href="<?php echo home_url('?'.http_build_query(['index'=>$_GET['index'],'id'=>$_GET['id']]));?>">更新日期</a></th>
                        </tr>
                        <?php
                        $sql = " select count(*)as count,(select option_value from " . $wpdb->prefix . "options where option_name='posts_per_page')as posts_per_page from " . $wpdb->prefix . "auto_movie m inner join " . $wpdb->prefix . "posts p on m.postid=p.ID where ".$sql_where." p.post_type='post' and post_status='publish' ; ";
                        $result_page = $wpdb->get_row($sql);
                        $posts_per_page = 20;
                        $startLimit = 0 == $paged ? 0 : ($paged - 1) * $posts_per_page;
                        $sql = " select (select size from " . $wpdb->prefix . "auto_movie_magnet where movieid=m.id order by seeders desc limit 1)size ,m.*,p.* from " . $wpdb->prefix . "auto_movie m inner join " . $wpdb->prefix . "posts p on m.postid=p.ID where ".$sql_where." p.post_type='post' and post_status='publish' order by " . $sql_order . " limit " . $startLimit . "," . $posts_per_page . "; ";
                        // var_dump($sql);
                        $results = $wpdb->get_results($sql);
                        if ($results) {
                            foreach ($results as $key => $value) {
                                ?>
                                <tr>
                                    <td><a class="indexlista" href="<?php echo the_permalink($value->postid); ?>" target="_blank"><?php echo $value->post_title; ?></a></td>
                                    <td>
                                        <div class="aligncenter"><?php
                                            $size = $value->size / 1024 / 1024 / 1024;
                                            if (!empty($size)) {
                                                echo round($value->size / 1024 / 1024 / 1024, 2) . 'G';
                                            } ?></div>
                                    </td>
                                    <td>
                                        <div class="aligncenter"><?php echo $value->maxSeeders; ?></div>
                                    </td>
                                    <td>
                                        <div class="aligncenter"><?php echo date("m-d", strtotime($value->a_addTime)); ?></div>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="100">
                                    <div class="aligncenter">暂时没有相关内容</div>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <div class="clear"></div>
                    <div class="navigation container">
                        <div class='pagination' style="text-align: center;">
                            <?php
                            $totalPage = (int)($result_page->count / $posts_per_page);  //取得分页总数
                            $buffCount = 3;                             //分页数左右偏离个数，
                            $maxPageCount = $buffCount * 2 + 1;             //分页数最多显示个数，一般是偏离数乘2加1
                            if ($paged <= $buffCount) {
                                $startPage = 1;
                            } elseif ($paged > $buffCount and $paged + $buffCount > $totalPage) {
                                $startPage = $totalPage - $maxPageCount + 1;
                            } else {
                                $startPage = $paged - $buffCount;
                            }
                            $startPage = $startPage < 1 ? 1 : $startPage;
                            if ($maxPageCount >= $totalPage) {
                                $endPage = $totalPage;
                            } else {
                                $endPage = $startPage + $maxPageCount - 1;
                            }
                            $endPage = $endPage > $totalPage ? $totalPage : $endPage;
                            if ($paged != 0 && $paged != 1) {
                                echo '<a href="' . get_pagenum_link(1) . '" class="extend" title="跳转到首页">首页</a>';
                                echo '<a href="' . get_pagenum_link($paged > 2 ? $paged - 1 : 1) . '" class="prev">上一页</a>';
                            }
                            for ($i = $startPage; $i <= $endPage; $i++) {
                                $paged = $paged == 0 ? 1 : $paged;
                                if ($i == $paged) {
                                    echo '<a class="current">' . $i . '</a>';
                                } else {
                                    echo '<a href="'. get_pagenum_link($i > 1 ? $i : 1) .'">' . $i . '</a>';
                                }
                            }
                            if ($paged < $totalPage) {
                                echo '<a href="' . get_pagenum_link($paged == 0 ? 2 : $paged + 1) . '" class="next">下一页</a>';
                                echo '<a href="' . get_pagenum_link($totalPage) . '" class="extend" title="跳转到最后一页">尾页</a>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<div class="clear"></div>
<?php get_footer() ?>
