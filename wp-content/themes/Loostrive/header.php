<?php
if(isset($_REQUEST["api"])){get_template_part('api/index');exit;}
if(isset($_REQUEST["auto"])){get_template_part('autopost/index');exit;}
if(isset($_REQUEST["download"]) && ($_REQUEST["download"]=="magnet" || $_REQUEST["download"]=="suburl")){get_template_part('download/index');exit;}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type');?>" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<?php include('includes/seo.php');?>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name');?> RSS Feed" href="<?php bloginfo('rss2_url');?>" />
<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name');?> Atom Feed" href="<?php bloginfo('atom_url');?>" />
<link rel="shortcut icon" href="<?php echo stripslashes(get_option('strive_favicon')); ?>" type="image/x-icon" />
<link rel="pingback" href="<?php bloginfo('pingback_url');?>" />
<!--[if lte IE 7]><script>window.location.href='http://7xkipo.com1.z0.glb.clouddn.com/upgrade-your-browser.html?referrer='+location.href;</script><![endif]-->
<?php @my_scripts_method; wp_head()?>
<?php flush()?>
<style>
	#post_container .fixed-hight .thumbnail{height:<?php echo stripslashes(get_option('strive_timthigh')); ?>px; overflow: hidden;}
	.related,.related_box{height: <?php echo stripslashes(get_option('strive_relatedhigh'))+'90'; ?>px;}
	.related_box .r_pic,.related_box .r_pic img {height: <?php echo stripslashes(get_option('strive_relatedhigh')); ?>px;}
</style>
</head>
<body  class="custom-background">
<?php if ( is_home() || is_search() || is_category() || is_month() || is_author() || is_archive() ) { ?>
<?php include('includes/loading.php'); ?>
<?php } ?>
		<div id="head" class="row">
        <?php if (get_option('strive_toolbar') == 'Display') { ?>
        	<div class="mainbar row">
                <div class="container">
                        <div id="topbar">
                            <?php if(function_exists('wp_nav_menu')) {
                            wp_nav_menu(array(
                            'theme_location'=>'toolbar',
                            'menu_id'=>'toolbar',
                            'container'=>'ul')
                            );}
                            ?>
                        </div>
                        <div id="rss">
                            <ul>
                                <li><a href="<?php bloginfo('rss2_url')?>" target="_blank" class="icon1" title="欢迎订阅<?php bloginfo('name');?>"></a></li>
                                <?php if (get_option('strive_sbaidu') == 'Display') { ?>
                                <li><a href="<?php echo stripslashes(get_option('strive_sbaidumap')); ?>" target="_blank" class="icon5" title="百度站点地图"></a></li><?php } else { } ?>
                                 <?php if(get_option('strive_tqq') == 'Display') { ?>
                                <li><a href="<?php echo stripslashes(get_option('strive_tqqurl')); ?>" target="_blank" class="icon2" title="我的腾讯微博" rel="nofollow"></a></li><?php } else { } ?>
                                <?php if(get_option('strive_weibo') == 'Display') { ?>
                                <li><a href="<?php echo stripslashes(get_option('strive_weibourl')); ?>" target="_blank" class="icon3" title="我的新浪微博" rel="nofollow"></a></li><?php } else { } ?>
                                <?php if(get_option('strive_site') == 'Display') { ?>
                                <li><a href="<?php echo stripslashes(get_option('strive_sitemap')); ?>" target="_blank" class="icon6" title="站点地图"></a></li><?php } else { } ?>
                            </ul>
                        </div>
                 </div>
             </div>
             <div class="clear"></div>
         <?php }else{?>
			<div class="row"></div>
		<?php }?>
				<div class="container">
					<div id="blogname" class="third">
                    	<a href="<?php bloginfo('url');?>/" title="<?php bloginfo('name');?>"><?php if ( is_home() || is_search() || is_category() || is_month() || is_author() || is_archive() ) { ?><h1><?php bloginfo('name');?></h1><?php } ?>
                        <img src="<?php echo stripslashes(get_option('strive_mylogo')); ?>" alt="<?php bloginfo('name');?>" /></a>
                    </div>
                 	<?php if (get_option('strive_logoadccode') == true) { ?>
                 	<div class="banner push-right">
                 	<?php echo stripslashes(get_option('strive_logoadccode')); ?>
					</div>
                	<?php } ?>
                </div>
				<div class="clear"></div>
		</div>
		<div class="mainmenus container">
			<div class="mainmenu">
				<div class="topnav">
					<?php if (get_option('strive_home') == 'Display') { ?>
                		<!--<a href="<?php bloginfo('url');?>" title="首页" class="<?php if ( is_home() ){ ?>home <?php } else {echo 'home_none'; } ?>">首页</a>-->
                        <div class="<?php if ( is_home() && !empty($_REQUEST['index']) && 'home'==$_REQUEST['index']){ ?>home<?php } else {echo 'home_none'; } ?>">
                            <a href="<?php bloginfo('url'); ?>?index=home" title="海报" style="float: left;height: 48px;width: 54px;color: white;text-align: center; margin-top: 10px;">海报</a>
                        </div>
                        <div class="<?php if ( is_home() && empty($_REQUEST['index'])){ ?>home <?php } else {echo 'home_none'; } ?>">
                            <a href="<?php bloginfo('url'); ?>?" title="列表" style="float: left;height: 48px;width: 54px;color: white;text-align: center; margin-top: 10px;">列表</a>
                        </div>
                        <div class="<?php if ( is_home() && !empty($_REQUEST['index']) && '4k'==$_REQUEST['index']){ ?>home_nav<?php } else {echo 'home_nav_none'; } ?>">
                            <a href="<?php bloginfo('url'); ?>?index=4k" title="4K" style="float: left;height: 48px;width: 54px;color: white;text-align: center;">4K</a>
                        </div>
                        <?php
                        $result = $wpdb->get_results("select * from {$wpdb->prefix}auto_thematic t where (select count(*) from " . $wpdb->prefix . "auto_movie m inner join " . $wpdb->prefix . "posts p on m.postid=p.ID where p.post_type='post' and post_status='publish' and m.thematic=t.id)>0; ");
                        if ($result) {
                            ?>
                            <ul>
                                <li>
                                    <a style="float: left;height: 48px;width: 54px;color: white;text-align: center;">专题</a>
                                    <ul>
                                        <?php
                                        foreach ($result as $key => $value) {
                                            ?>
                                            <li><a href="/?index=thematic&id=<?php echo $value->id; ?>"><?php echo $value->name; ?></a></li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            </ul>

                            <?php
                        }
                    }
                    ?>
                    <div class="menu-button"><i class="menu-ico"></i></div>
                    	<?php if(function_exists('wp_nav_menu')) {wp_nav_menu(array('theme_location'=>'nav','container'=>'ul'));}?>
               <?php if (get_option('strive_menusearch') == 'Display') { ?>
                <ul class="menu-right">
                    <li class="menu-search">
                    	<a href="#" id="menu-search" title="搜索"></a>
                    	<div class="menu-search-form ">
							<form action="<?php bloginfo('url');?>" method="get">
                            	<input name="s" type="text" id="search" value="" maxlength="150" placeholder="请输入搜索内容" x-webkit-speech style="width:135px">
                            	<input type="submit" value="搜索" class="button"/>
                            </form>
                        </div>
                    </li>
                </ul>
                <?php }?>
                 <!-- menus END -->
            </div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<?php if(is_mobile()){?><div class="adphone container"><div class="row"><?php echo stripslashes(get_option('strive_adphone')); ?></div></div><?php }?>
