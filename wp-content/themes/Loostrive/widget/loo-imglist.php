<?php
add_action('widgets_init', 'loo_imglist');

function loo_imglist() {
    register_widget('loo_imglist');
}

class loo_imglist extends WP_Widget {
    function loo_imglist() {
        $widget_ops = array('classname' => 'loo_imglist', 'description' => '图文展示（最新文章+热门文章+随机文章）');
        $this->WP_Widget('loo_imglist', 'Loome-图文展示', $widget_ops, $control_ops);
    }

    function widget($args, $instance) {
        extract($args);

        $title = apply_filters('widget_name', $instance['title']);
        $limit = $instance['limit'];
        $cat = $instance['cat'];
        $orderby = $instance['orderby'];
        $img = $instance['img'];
        $height = $instance['height'];

        $style = '';
        echo $before_widget;
        echo $before_title . $title . $after_title;
        echo '<div class="siderbar-list' . $style . '"><ul class="imglist clear">';
        echo ltheme_posts_list($orderby, $limit, $cat, $height);
        echo '</ul></div>';
        echo $after_widget;
    }

    function form($instance) {
        ?>
        <p>
            <label>
                标题：
                <input style="width:100%;" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>"/>
            </label>
        </p>
        <p>
            <label>
                排序：
                <select style="width:100%;" id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>" style="width:100%;">
                    <option value="comment_count" <?php selected('comment_count', $instance['orderby']); ?>>评论数</option>
                    <option value="date" <?php selected('date', $instance['orderby']); ?>>发布时间</option>
                    <option value="rand" <?php selected('rand', $instance['orderby']); ?>>随机</option>
                    <option value="a_upTime" <?php selected('a_upTime', $instance['orderby']); ?>>自定义最近更新</option>
                    <option value="maxSeeders" <?php selected('maxSeeders', $instance['orderby']); ?>>自定义热门</option>
                </select>
            </label>
        </p>
        <p>
            <label>
                分类限制：
                <a style="font-weight:bold;color:#f60;text-decoration:none;" href="javascript:;" title="格式：1,2 &nbsp;表限制ID为1,2分类的文章&#13;格式：-1,-2 &nbsp;表排除分类ID为1,2的文章&#13;也可直接写1或者-1；注意逗号须是英文的">？</a>
                <input style="width:100%;" id="<?php echo $this->get_field_id('cat'); ?>" name="<?php echo $this->get_field_name('cat'); ?>" type="text" value="<?php echo $instance['cat']; ?>" size="24"/>
            </label>
        </p>
        <p>
            <label>
                显示数目：
                <input style="width:100%;" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="number" value="<?php echo $instance['limit']; ?>" size="24"/>
            </label>
        </p>
        <p>
            <label>
                缩略图高度(px)：
                <input style="width:100%;" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="number" value="<?php echo $instance['height']; ?>" size="24"/>
            </label>
        </p>
        <?php
    }
}

function ltheme_posts_list($orderby, $limit, $cat, $height) {
    global $wpdb;
    switch ($orderby) {
        case 'a_upTime':
            $sql = " select p.* from " . $wpdb->prefix . "auto_movie m inner join " . $wpdb->prefix . "posts p on m.postid=p.ID where p.post_type='post' and post_status='publish' order by m.a_upTime desc limit " . $limit . "; ";
            $results = $wpdb->get_results($sql);
            //var_dump($results);
            if ($results) {
                global $post;
                foreach ($results as $post) {
                    setup_postdata($post);
                    ?>
                    <li class="post" style="min-height: <?php echo $height . '50'; ?>px">
                        <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php if (!$height == '') {
                                echo post_thumbnail_img(142, $height);
                            } else {
                                echo post_thumbnail_img(142, 95);
                            } ?>
                            <h4><?php the_title(); ?></h4></a>
                    </li>
                    <?php
                }
                //重置 setup_postdata()的反函数
                wp_reset_postdata();
            }
            break;
        case 'maxSeeders':
            $sql = " select p.* from " . $wpdb->prefix . "auto_movie m inner join " . $wpdb->prefix . "posts p on m.postid=p.ID where p.post_type='post' and post_status='publish' order by m.maxSeeders desc limit " . $limit . "; ";
            $results = $wpdb->get_results($sql);
            //var_dump($results);
            if ($results) {
                ?>
                <style type="text/css">
                    .container .imglist li img {
                        width: 100%;
                    }
                </style>
                <?php
                global $post;
                foreach ($results as $post) {
                    setup_postdata($post);
                    $content = $post->post_content;
                    preg_match_all('/<img.*?(?: |\\t|\\r|\\n)?src=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>/sim', $content, $strResult, PREG_PATTERN_ORDER);
                    $n = count($strResult[1]);
                    ?>
                    <li class="post" style="min-height: <?php echo $height + '0'; ?>px;width: 155px;">
                        <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                            <?php if (1 == 2): ?>
                                <img src="<?php echo get_bloginfo("template_url"); ?>/timthumb.php?src=<?php echo $strResult[1][0] ?>&w=0&h=1&zc=1" height="230" alt="<?php the_title(); ?>"/>
                            <?php endif; ?>
                            <img src="/timthumb_300/<?php $parse_url = parse_url(str_replace('.doubanio.com','',$strResult[1][0])); echo $parse_url['host'] . $parse_url['path']; ?>" height="230" alt="<?php the_title(); ?>"/>
                        </a>
                    </li>
                    <?php
                }
                //重置 setup_postdata()的反函数
                wp_reset_postdata();
            }
            break;
        default:
            $args = array(
                'order'            => DESC,
                'cat'              => $cat,
                'orderby'          => $orderby,
                'showposts'        => $limit,
                'caller_get_posts' => 1
            );
            query_posts($args);
            while (have_posts()) : the_post();
                ?>
                <li class="post" style="min-height: <?php echo $height + '50'; ?>px">
                    <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php if (!$height == '') {
                            echo post_thumbnail_img(142, $height);
                        } else {
                            echo post_thumbnail_img(142, 95);
                        } ?>
                        <h4><?php the_title(); ?></h4></a>
                </li>
            <?php
            endwhile;
            wp_reset_query();
    }
}

?>
