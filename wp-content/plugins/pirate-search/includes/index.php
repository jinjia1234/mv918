<?php
class wp_pirate {
    private $config;
    private $db;
    public function __construct() {
        global $config, $wpdb;
        $this->config = $config;
        $this->db = $wpdb;
        include_once('getPirate.class.php');
    }

    public function wp_search(){
        $s = isset($_REQUEST['s']) ? json_decode(json_encode($_REQUEST['s'])) : '' ;
        if(!empty($s->k)){
            $getPirate = new getPirate();
            $getPirate->config['limit'] = 10;
            $url ='https://thehiddenbay.com/search/'.$s->k.'/1/99/0';
            $results_list = $getPirate->get_list($url);
            //var_dump($results_list);
        }
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">海盗湾</h1>
            <!--
            <a href="http://www.docu918.xyz/wp-admin/post-new.php" class="page-title-action">写文章</a>
            <hr class="wp-header-end">
            <h2 class='screen-reader-text'>过滤文章列表</h2>
            <ul class='subsubsub'>
                <li class='all'><a href="edit.php?post_type=post" class="current">全部<span class="count">（1）</span></a> |</li>
                <li class='publish'><a href="edit.php?post_status=publish&#038;post_type=post">已发布<span class="count">（1）</span></a>
                </li>
            </ul>
            -->
            <form id="posts-filter" method="get">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>">
                <div class="tablenav top">
                    <!--
                    <div class="alignleft actions bulkactions">
                        <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label><select name="action" id="bulk-action-selector-top">
                            <option value="-1">批量操作</option>
                            <option value="edit" class="hide-if-no-js">编辑</option>
                            <option value="trash">移至回收站</option>
                        </select>
                        <input type="submit" id="doaction" class="button action" value="应用"/>
                    </div>
                    -->
                    <style>
                        #your-profile label + a, .wp-admin select, fieldset label, label{vertical-align: baseline;}
                        .wp-admin .screen-per-page{width: 3em;}
                        input::-webkit-outer-spin-button,
                        input::-webkit-inner-spin-button { -webkit-appearance: none; }
                        input[type="number"] { -moz-appearance: textfield; }
                        input::-moz-placeholder{ color: #d9dfe4; }
                    </style>
                    <div class="alignleft">
                        <!--
                        <input id="seeders-min-input" type="number" step="1" min="0" max="999" class="screen-per-page" name="s[s_in]" maxlength="3" value="<?php echo $s->s_in; ?>"/>
                        <label for="seeders-min-input">&lt;种子&lt;</label>
                        <input id="seeders-max-input" type="number" step="1" min="0" max="999" class="screen-per-page" name="s[s_ax]" maxlength="3" value="<?php echo $s->s_ax; ?>"/>
                        <input id="leechers-min-input" type="number" step="1" min="0" max="999" class="screen-per-page" name="s[l_in]" maxlength="3" value="<?php echo $s->l_in; ?>"/>
                        <label for="leechers-min-input">&lt;连接&lt;</label>
                        <input id="leechers-max-input" type="number" step="1" min="0" max="999" class="screen-per-page" name="s[l_ax]" maxlength="3" value="<?php echo $s->l_ax; ?>"/>
                        <input id="size-min-input" type="number" step="1" min="0" max="999" class="screen-per-page" name="s[size_in]" maxlength="3" value="<?php echo $s->size_in; ?>"/>
                        <label for="size-min-input">&lt;大小&lt;</label>
                        <input id="size-max-input" type="number" step="1" min="0" max="999" class="screen-per-page" name="s[size_ax]" maxlength="3" value="<?php echo $s->size_ax; ?>"/>
                        -->
                        <!--
                        <input id="date-min-input" type="text" style="width: 84px;" name="s[d_in]" maxlength="10" value="<?php echo $s->d_in; ?>" style="color: #e4e2e2;" placeholder="<?php echo date("Y",time()).'-01-01'; ?>"/>
                        <label for="date-min-input">&lt;日期&lt;</label>
                        <input id="date-max-input" type="text" style="width: 84px;" name="s[d_ax]" maxlength="10" value="<?php echo $s->d_ax; ?>" placeholder="<?php echo date("Y-m-d",time()); ?>"/>
                        -->
                        <!--
                        <select name="s[ls]">
                            <?php
                            //foreach ($config_Rarbg['loaclSort'] as $key=>$value){
                            //    if($p->ls==$value){
                            //        echo '<option value="'.$value.'" selected="selected">'.$key.'</option>';
                            //    }else{
                            //        echo '<option value="'.$value.'">'.$key.'</option>';
                            //    }
                            //}
                            //?>
                        </select>
                        -->
                        <!--<input type="submit" name="filter_action" id="post-query-submit" class="button" value="筛选"/>-->
                    </div>
                    <p class="search-box">
                        <!--
                        <select name="s[c]">
                            <?php
                            //foreach ($config_Rarbg['category'] as $key=>$value){
                            //    if($c==$value){
                            //        echo '<option value="'.$value.'" selected="selected">'.$key.'</option>';
                            //    }else{
                            //        echo '<option value="'.$value.'">'.$key.'</option>';
                            //    }
                            //}
                            ?>
                        </select>
                        <select name="s[s]">
                            <?php
                            //foreach ($config_Rarbg['sort'] as $key=>$value){
                            //    if($s==$value){
                            //        echo '<option value="'.$value.'" selected="selected">'.$key.'</option>';
                            //    }else{
                            //        echo '<option value="'.$value.'">'.$key.'</option>';
                            //    }
                            //}
                            ?>
                        </select>
                        -->
                        <input type="search" id="post-search-input" style="float:initial;width:380px;" name="s[k]" value="<?php echo $s->k; ?>"/>
                        <button type="submit" id="search-submit" class="button" value="Search"/><span class="dashicons dashicons-search" style="margin-top:5px;width:19px;"></span></button>
                    </p>
                    <!--
                    <div class='tablenav-pages one-page'><span class="displaying-num">1个项目</span>
                        <span class='pagination-links'><span class="tablenav-pages-navspan" aria-hidden="true">&laquo;</span>
        <span class="tablenav-pages-navspan" aria-hidden="true">&lsaquo;</span>
        <span class="paging-input">第<label for="current-page-selector" class="screen-reader-text">当前页</label><input class='current-page' id='current-page-selector' type='text' name='paged' value='1' size='1' aria-describedby='table-paging'/><span class='tablenav-paging-text'>页，共<span class='total-pages'>1</span>页</span></span>
        <span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span>
        <span class="tablenav-pages-navspan" aria-hidden="true">&raquo;</span></span></div>
                    -->
                    <br class="clear"/>
                </div>
            </form>
            <form id="posts-list" method="get">
                <h2 class='screen-reader-text'>文章列表</h2>
                <table class="wp-list-table widefat fixed striped posts">
                    <thead>
                    <tr>
                        <td id='cb' class='manage-column column-cb check-column'>
                            <label class="screen-reader-text" for="cb-select-all-1">全选</label><input id="cb-select-all-1" type="checkbox"/>
                        </td>
                        <th scope="col" width="60%" class='manage-column column-primary'>名称</th>
                        <th scope="col" class='manage-column'><div class='textright'>文件大小</div></th>
                        <th scope="col" class='manage-column'><div class='textright'>种子数</div></th>
                        <th scope="col" class='manage-column'><div class='textright imdb'>IMDB</div></th>
                        <th scope="col" class='manage-column'><div class='num'>发布日期</div></th>
                    </tr>
                    </thead>
                    <tbody id="the-list">
                    <?php
                    if(!$results_list){
                        echo '<tr class="no-items"><td class="colspanchange" colspan="6">未找到内容。</td></tr></tbody>';
                    }else{
                        //if(!empty($s->ls) && strpos($s->ls,'date')>-1){
                        //    foreach($results_list as $key1=>$value1){
                        //        foreach($value1 as $key2=>$value2){
                        //            $arrTemp[$key2][$key1] = $value2;
                        //        }
                        //    }
                        //    array_multisort($arrTemp['pubdate'],constant(str_ireplace('date','SORT',strtoupper($s->ls))),$results_list);
                        //}
                        //if(!empty($s->ls) && strpos($s->ls,'size')>-1){
                        //    foreach($results_list as $key1=>$value1){
                        //        foreach($value1 as $key2=>$value2){
                        //            $arrTemp[$key2][$key1] = $value2;
                        //        }
                        //    }
                        //    array_multisort($arrTemp['size'],constant(str_ireplace('size','SORT',strtoupper($s->ls))),$results_list);
                        //}
                        $i = 0 ;
                        foreach($results_list as $key=>$value){
                            //if(!empty($s->s_ax) && (int)$s->s_ax>0){
                            //    if($value->seeders>=$s->s_ax){continue;}
                            //}
                            //if(!empty($s->l_ax) && (int)$s->l_ax>0){
                            //    if($value->leechers>=$s->l_ax){continue;}
                            //}
                            //if(!empty($s->size_in) && (float)$s->size_in>0){
                            //    if(round((($value->size/1024)/1024)/1024,2)<=$s->size_in){continue;}
                            //}
                            //if(!empty($s->size_ax) && (float)$s->size_ax>0){
                            //    if(round((($value->size/1024)/1024)/1024,2)>=$s->size_ax){continue;}
                            //}
                            //if(!empty($s->d_in) && strtotime($s->d_in)){
                            //    if(date("Y-m-d",strtotime($value->pubdate))<=$s->d_in){continue;}
                            //}
                            //if(!empty($s->d_ax) && strtotime($s->d_ax)){
                            //    if(date("Y-m-d",strtotime($value->pubdate))>=$s->d_ax){continue;}
                            //}
                            ?>
                            <tr id="post-1" class="iedit author-self level-0 post-1 type-post status-publish format-standard hentry category-uncategorized">
                                <th scope="row" class="check-column">
                                    <input type="checkbox" name="post[<?php echo $i; ?>][list]" value='<?php echo json_encode($value,320);//echo base64_encode(json_encode($value));?>' disabled>
                                    <input type="hidden" name="post[<?php echo $i; ?>][detail]" disabled>
                                </th>
                                <td class="title column-title has-row-actions column-primary page-title">
                                    <div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
                                    <a href="<?php echo $value['detail_url']; ?>" target="_blank"><?php echo $value['title']; ?></a>
                                </td>
                                <td class='textright'><?php echo $value['size']; ?></td>
                                <td class='textright'><a href="<?php echo $value['magnet']; ?>"><?php echo $value['seeders']; ?></a></td>
                                <td class='textright' detail_url="<?php echo $value['detail_url']; ?>"><?php echo $value['imdb']; ?></td>
                                <td class='num'><abbr title="<?php echo $value['update']; ?>"><?php echo $value['update']; ?></abbr></td>
                            </tr>
                            <?php
                            $i++;
                        }
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td class='manage-column column-cb check-column'>
                            <label class="screen-reader-text" for="cb-select-all-2">全选</label><input id="cb-select-all-2" type="checkbox"/>
                        </td>
                        <th scope="col" class='manage-column column-primary'>名称</th>
                        <th scope="col" class='manage-column'><div class="textright">文件大小</div></th>
                        <th scope="col" class='manage-column'><div class='textright'>种子数</div></th>
                        <th scope="col" class='manage-column'><div class='textright imdb'>IMDB</div></th>
                        <th scope="col" class='manage-column'><div class='num'>发布日期</div></th>
                    </tr>
                    </tfoot>
                </table>
                <div class="tablenav bottom">
                    <div class="alignleft actions bulkactions">
                        <label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label><select name="terms" id="bulk-action-selector-bottom">
                            <option value="0">发布到</option>
                            <?php
                            $args = array(
                                'type' => 'post',
                                'child_of' => 0,
                                'parent' => '',
                                'orderby' => 'name',
                                'order' => 'ASC',
                                'hide_empty' => 1,
                                'hierarchical' => 1,
                                'exclude' => '',
                                'include' => '',
                                'number' => '',
                                'hide_empty' =>0,
                                'taxonomy' => 'category',
                                'pad_counts' => false
                            );
                            $categories = get_categories( $args );
                            foreach($categories as $key=>$value){
                                echo '<option value="'.$value->term_id.'" class="hide-if-no-js">'.$value->name.'</option>';
                            }
                            ?>
                        </select>
                        <input type="button" id="doaction2" class="button action" value="应用"/>
                    </div>
                    <div class="alignleft actions">
                    </div>
<!--                    <script type="text/javascript" src="https://gamecf.cn/jquery/3.2.1/jquery.min.js"></script>-->
                    <script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__).'jquery-3.3.1.min.js'; ?>"></script>
<!--                    <script type="text/javascript" src="./jquery-3.3.1.min.js"></script>-->
                    <script type="text/javascript">
                        $(document).ready(function(){
                            $('.imdb').one('click',function () {
                                $('.imdb').unbind();
                                $(this).parents('table').find('tbody > tr').each(function(){
                                    var td_imdb = $(this).find('td:eq(3)');
                                    var th = $(this).find('th');
                                    $.ajax({
                                        type: 'post',
                                        url: '?page=<?php echo $this->config['page']; ?>&action=ajax_detail',
                                        data: {url: td_imdb.attr('detail_url')},
                                        dataType: 'json',
                                        beforeSend: function () {
                                            td_imdb.html('<span class="spinner is-active"></span>');
                                        },
                                        success: function (result) {
                                            if (typeof(result) == 'object' && result.imdb != '') {
                                                td_imdb.html(result.imdb);
                                                th.find('input').attr('disabled', false);
                                                th.find('input[type="hidden"]').val(JSON.stringify(result));
                                            } else {
                                                td_imdb.html('');
                                            }
                                        },
                                        error: function () {
                                            td_imdb.html('');
                                        },
                                    });
                                });
                            });
                            $("#doaction2").click(function(){
                                var action = $("#bulk-action-selector-bottom").val();
                                if(action!=0){
                                    var checkbox = 0 ;
                                    $("input[type='checkbox']:checked").each(function(){
                                        // alert($(this).val());
                                        checkbox++;
                                    });
                                    if(checkbox>0){
                                        $.ajax({
                                            type: 'post',
                                            url: "?page=<?php echo $this->config['page']; ?>&action=form_submit",
                                            data: $("#posts-list").serialize(),
                                            dataType: "json",
                                            beforeSend: function () {
                                                $("#doaction2").val("正在应用");
                                            },
                                            success: function(data){
                                                alert(data.msg);
                                                $("#doaction2").val("应用");
                                            },
                                            error : function() {
                                                alert("异常！");
                                            },
                                        });
                                    }else{
                                        alert("请选择要操作的记录！");
                                    }
                                }else{
                                    alert("请选择发布的分类！");
                                }
                            });
                        });
                    </script>
                    <!--
                    <div class='tablenav-pages one-page'><span class="displaying-num">1个项目</span>
                        <span class='pagination-links'><span class="tablenav-pages-navspan" aria-hidden="true">&laquo;</span>
        <span class="tablenav-pages-navspan" aria-hidden="true">&lsaquo;</span>
        <span class="screen-reader-text">当前页</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">第1页，共<span class='total-pages'>1</span>页</span></span>
        <span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span>
        <span class="tablenav-pages-navspan" aria-hidden="true">&raquo;</span></span></div>
                    <br class="clear"/>
                    -->
                </div>
            </form>
            <div id="ajax-response"></div>
            <br class="clear"/>
        </div>
        <?php
    }

    public function ajax_detail($url=''){
        if($url){
            $getPirate = new getPirate();
            $result = $getPirate->get_details($url);
            //var_dump($result);
            die(json_encode($result,30));
        }else{
            die(json_encode(['code'=>1,'msg'=>'emtpy url'],320));
        }
    }

    public function from_submit(){
        $terms = $_REQUEST['terms'];
        $post = $_REQUEST['post'];
        if ($post) {
            $data = [];
            foreach ($post as $key => $value) {
                $data[] = array_merge((array)json_decode($value['list'], true), (array)json_decode($value['detail'], true));
            }
            if ($data) {
                try {
                    foreach ($data as $key => $value) {
                        $data_a = [[
                            'title'    => $value['title'],
                            'download' => $value['magnet'],
                            'seeders'  => $value['seeders'],
                            'leechers' => $value['leechers'],
                            'size'     => $value['size'],
                            'pubdate'  => $value['update'],
                        ]];
                        $result = $this->db->get_row("SELECT * FROM " . $this->db->prefix . "auto_movie WHERE imdb='" . $value['imdb'] . "'; ");
                        if ($result) {
                            if ($a = json_decode($result->a, true)) {
                                if (false == array_search($value['title'], array_column($a, 'title'))) {
                                    $this->db->update($this->db->prefix . "auto_movie", [
                                        'term'     => $terms,
                                        'a'        => json_encode(array_merge($a, $data_a), 320),
                                        'a_upTime' => date("Y-m-d H:i:s"),
                                    ], ['imdb' => $value['imdb']]);
                                }
                            }
                        } else {
                            $this->db->insert($this->db->prefix . "auto_movie", [
                                'term' => $terms,
                                'imdb' => $value['imdb'],
                                'a'    => json_encode($data_a, 320),
                            ]);
                        }
                    }
                } catch (Exception $e) {
                    echo json_encode(['msg' => $e->getMessage()], 320);
                }
                echo json_encode(['msg' => '操作成功,等待采集自动完成'], 320);
            }
        }
    }
}
