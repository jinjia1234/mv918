<div class="clear"></div>
<div id="footer">
  <?php if (get_option('strive_footnav') == 'Display') { ?>
  <div class="footnav container">
    <?php if(function_exists('wp_nav_menu')) {wp_nav_menu(array('theme_location'=>'footnav','menu_id'=>'footnav','container'=>'ul'));}?>
  </div>
  <?php } else {} ?>
  <?php if (get_option('strive_flinks') == 'Display') { ?>
  <?php wp_reset_query(); if ( is_home() ) { ?> 
  <div class="footnav container">
    <?php if(function_exists('wp_nav_menu')) {wp_nav_menu(array('theme_location'=>'friendlink','menu_id'=>'friendlink','container'=>'ul'));}?>
  </div>
  <?php } ?>
  <?php } else {} ?>
  <div class="copyright">
  <p> Copyright <?php echo comicpress_copyright();?> <a href="<?php bloginfo('url');?>/"><strong>
    <?php bloginfo('name');?>
    </strong></a> <br />蜀ICP备 17017580号-35 Powered by <a href="http://www.mv918.com/" rel="external">mv918.com</a><br />
	
    <?php if (get_option('strive_beian') == 'Display') { ?>
    <a href="http://www.miitbeian.gov.cn/" rel="external"><?php echo stripslashes(get_option('strive_beianhao')); ?></a>
    <?php { echo '.'; } ?>
    <?php } else {} ?>
    <?php if (get_option('strive_tj') == 'Display') { ?>
    <?php echo stripslashes(get_option('strive_tjcode')); ?>
    <?php { echo ' '; } ?>
    <?php } else {} ?>
  </p>
  <!--
  <p class="author"><a href="http://www.mv918.com" target="_blank" rel="external">MV918</a></p>
  -->
  </div>
</div>
</div>
<!--gototop-->
<div id="tbox">
  <?php if( is_single() || is_page()){?>
  <a id="home" href="<?php bloginfo('url');?>"></a>
  <?php } ?>
  <?php if( is_single() || is_page() && comments_open() ){ ?>
  <a id="pinglun" href="#comments_box"></a>
  <?php } ?>
  <a id="gotop" href="javascript:void(0)"></a> </div>
<?php wp_footer(); ?>

<?php if (get_option('strive_bdshare') == 'Display'&&is_home()) { ?>
	<script>window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"0","bdSize":"16"},"share":{}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?cdnversion='+~(-new Date()/36e5)];</script>
<?php }?>
<?php if (get_option('strive_bdshare') == 'Display'&&is_single()) { ?>
	<script>window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"0","bdSize":"24"},"share":{}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?cdnversion='+~(-new Date()/36e5)];</script>
<?php }?>

<?php
if(is_single()){
    $result = $wpdb->get_row(" select imdb from ".$wpdb->prefix."auto_movie where postid='{$id}' limit 1 ");
    $imdb = $result->imdb;
    ?>
    <script>
        /**
         * 对象转url参数
         * @param {*} data
         */
        function urlencode(data) {
            let _result = [];
            for (let key in data) {
                let value = data[key];
                // 去掉为空的参数
                if (['', undefined, null].includes(value)) {
                    continue;
                }
                _result.push('magnet_title[]=' + encodeURIComponent(value));
            }
            return _result.length ? _result.join('&') : '';
        }
        var post_content = $('#post_content').html();
        var magnet_title = [];
        post_content.replace(/<h2>(?:[\s\S]*?)\">(.*?)<\/a>(?:[\s\S]*?)<\/h2>/g, function (fullPattern, group1, group2, group3) {
            magnet_title.push(group1);
        });
        $(document).ready(function () {
            $.getJSON('/down/subtitle/subtitle_index.php?movie_imdb=<?php echo $imdb; ?>&' + urlencode(magnet_title), function (response) {
                $('#post_content > h2').each(function (index) {
                    var h2_html = $(this).html();
                    response.forEach((item, index) => {
                        if (h2_html.indexOf(item.title) != -1) {
                            if(item.share_file_key) {
                                $(this).html(h2_html + " &nbsp; <a class='down_link' href='https://089u.com/f/22302351-" + item.share_file_id + (item.share_file_key ? '-' + item.share_file_key : '') + "?p=mv918' target='_blank'>下载字幕(密码:mv918)</a>");
                            }else{
                                $(this).html(h2_html + " &nbsp; <a class='down_link' href='https://089u.com/file/22302351-" + item.share_file_id + "' target='_blank'>下载字幕</a>");
                            }
                            return ;
                        }
                    });
                });
            });
            $(document).on('click','.down_link',function () {
                var that = $(this);
                var password = $(this).data('password');
                if (!password) {
                    var url = $(this).attr('href');
                    var ids = url.split('/').pop().split('-');
                    if(ids.length == 3){
                        $.ajax({
                            type: 'GET',
                            url: "https://webapi.ctfile.com/passcode.php?file_id=" + ids[1] + "&folder_id=0&userid=" + ids[0] + "&passcode=mv918&r=" + Math.random(),
                            xhrFields: {
                                withCredentials: true
                            },
                            crossDomain: true,
                            dataType: 'json',
                            success: function (data) {
                                that.attr('data-password', 'mv918');
                                console.log(data);
                            }
                        });
                    }
                }
            });
        });
    </script>
    <?php
}
if(is_home()){
    echo autoAjax("abcd");
}elseif(is_category() || is_single()){
    echo autoAjax("abcd");
}
function autoAjax($auto=false){
    return <<<autoAjax
<div id="myDiv"></div>
<script type="text/javascript">
    $(function(){
        $.post(window.location.href,{auto:"{$auto}"},function(data){/*\$('#myDiv').html(data);*/},"text");
    });
</script>
autoAjax;
}
?>
<script type="text/javascript">
    var rand = parseInt(2 * Math.random());
    switch (rand) {
        case 2:
            break;
        case 1:
            break;
        default:
    }
</script>
<script>
    var _hmt = _hmt || [];
    (function () {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?9c8fead9d61297b4ec8e80142d0bed46";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
<script>
    !(function(c,i,e,b){
        var h=i.createElement("script");
        var f=i.getElementsByTagName("script")[0];
        h.type="text/javascript";
        h.crossorigin=true;
        h.onload=function(){new c[b]["Monitor"]().init({id:"1yH9bhfab98rGBwX"});};
        f.parentNode.insertBefore(h,f);h.src=e;})(window,document,"https://sdk.51.la/perf/js-sdk-perf.min.js","LingQue");
</script>
<script async src="https://www.googletagmanager.com/gtag/js?id=G-1MVH0P6EJR"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-1MVH0P6EJR');
</script>
</body></html>


