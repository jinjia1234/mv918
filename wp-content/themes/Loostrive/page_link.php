<?php
/*
Template Name: 友情链接
*/
?>
<?php get_header(); ?>
<div class="container">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    $(".flink a").each(function(e) {
                        $(this).prepend("<img src=https://statics.dnspod.cn/proxy_favicon/_/favicon?domain=" + this.href.replace(/^(http:\/\/[^\/]+).*$/, '$1').replace('http://', '') + ">");
                    });
                });
            </script>
            <?php if (get_option('strive_breadcrumb') == 'Display') { ?>
                <div class="subsidiary box">
                    <div class="bulletin fourfifth">
                        <span class="sixth">当前位置：</span><?php loo_breadcrumbs(); ?>
                    </div>
                </div>
            <?php } ?>
            <?php get_sidebar(); ?>
            <div class="mainleft">
                <div class="article_container row  box">
                    <h1 class="page_title">链接申请细则</h1>
                    <div class="context">
                        <div id="post_content cont_none">
                            <p>一、在您申请本站友情链接之前请先做好本站链接，否则不会通过，谢谢</p>
                            <p>二、如果您的站还未被baidu或google收录，申请链接暂不予受理</p>
                            <p>三、本站链接名称：<a href="<?php bloginfo('siteurl'); ?>/"><?php bloginfo('name'); ?></a></p>
                            <p>四、本站链接地址：<a href="<?php bloginfo('siteurl'); ?>/"><?php bloginfo('siteurl'); ?>/</a></p>
                            <p>做好本站链接后请在这下面，我们会在24小时之内添加上你的链接</p>
                        </div>
                    </div>
                </div>
                <div id="comments_box">
                    <?php comments_template(); ?>
                </div>
        <?php endwhile;
    endif; ?>
            </div>
</div>
<?php get_footer(); ?>