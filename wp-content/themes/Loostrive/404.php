<?php get_header(); ?>
    <div class="container">
        <div class="subsidiary row box">
            <div class="bulletin fourfifth">
                当前位置：<a href="<?php bloginfo('siteurl'); ?>/" title="返回首页">首页</a> > 未知页面
            </div>
        </div>
        <div class="mainleft">
            <div class="article_container row  box">
                <div class="third centered" style="text-align:center; margin:50px auto;">
                    <h2>
                        <center>抱歉，您打开的页面未能找到。</center>
                    </h2>
                    <div class="context">
                        <center><a href="<?php bloginfo('siteurl'); ?>" title="返回首页"><img src="<?php bloginfo('template_directory'); ?>/images/404.gif" alt="Error 404 - Not Found"/></a></center>
                    </div>
                    <h2>
                        <center><span id="totalSecond">3</span>秒后自动返回首页</center>
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <script language="javascript" type="text/javascript">
        <!--
        var second = document.getElementById('totalSecond').textContent;
        if (navigator.appName.indexOf("Explorer") > -1)  //判断是IE浏览器还是Firefox浏览器，采用相应措施取得秒数
        {
            second = document.getElementById('totalSecond').innerText;
        } else {
            second = document.getElementById('totalSecond').textContent;
        }
        var interval = setInterval("redirect()", 1000);  //每1秒钟调用redirect()方法一次
        var count = 0;
        function redirect() {
            if(count == 0){
                location.href = '<?php bloginfo('siteurl'); ?>';
            }
            if (second < 0) {
                clearInterval(interval);
            } else {
                if (navigator.appName.indexOf("Explorer") > -1) {
                    document.getElementById('totalSecond').innerText = second--;
                } else {
                    document.getElementById('totalSecond').textContent = second--;
                }
            }
            count++;
        }
        -->
    </script>
    </body>
<?php get_footer(); ?>