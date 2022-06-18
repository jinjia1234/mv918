<?php/** * Plugin Name: Manually set subtitles * Plugin URI: http://tt3p.com * Description: 手动设置字幕。 * Version: 1.0.0 * Author: QQ1716001590 * Author URI: http://blog.tt3p.com * License: GPLv2 *//* Copyright 2017 QQ1716001590 (email : 1716001590@qq.com)This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty ofMERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA*/register_activation_hook( __FILE__, 'subtitles_link_install');register_deactivation_hook( __FILE__, 'subtitles_link_remove' );register_uninstall_hook( __FILE__, 'subtitles_link_remove' );if( is_admin() ) {    add_action('plugins_loaded','post_row_actions_subtitles_link_page');    function post_row_actions_subtitles_link_page(){        $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "" ;        $postid = isset($_REQUEST["postid"]) ? $_REQUEST["postid"] : 0 ;        if(!empty($page) && $page=="post_row_actions_subtitles_link" && !empty($postid) && (int)$postid>0){            global $wpdb;            $ret = $wpdb->get_results(" select a,postid,thematic from ".$wpdb->prefix."auto_movie where postid='".$postid."' limit 1 ; ");            if($ret){                foreach($ret as $key1=>$value1){                    $data_movie_a = json_decode($value1->a);                    $thematic_selected = $value1->thematic ? 'selected="selected"' : null ;                    echo '<style type="text/css">   .border-table { border-collapse: collapse; border: none; }   .border-table td { border: solid #bbb 1px; }input[type="text"]{    border: 1px solid #eee;    border-radius: 2px;    color: #000;    font-family: "Open Sans", sans-serif;    font-size: 1em;    height: 30px;    padding: 0 5px;    transition: background 0.3s ease-in-out;    width: 100%;}input:focus {    outline: none;    border-color: #9ecaed;    box-shadow: 0 0 10px #9ecaed;}input[type="checkbox"]{width:13px;}</style>                      ';                    echo '<table width="100%" border="1" cellpadding="5" cellspacing="0" id="'.$value1->postid.'" class="border-table">';                    ?>                    <tr>                        <td colspan="3">                            <select name="thematic">                                <option value=""></option>                                <?php                                $result = $wpdb->get_results(" select * from {$wpdb->prefix}auto_thematic ; ");                                if ($result) {                                    foreach ($result as $key=>$value){                                    ?>                                    <option value="<?php echo $value->id; ?>" <?php echo $thematic_selected; ?>><?php echo $value->name; ?></option>                                    <?php                                    }                                }                                ?>                                ?>                            </select>                        </td>                    </tr>                    <?php                    foreach ($data_movie_a as $key2 => $value2) {                        echo '<tr><td rowspan="2" align="center">' . ($key2 + 1) . '</td><td>' . $value2->title . '</td><td align="center">' . round((($value2->size / 1024) / 1024) / 1024, 2) . 'G</td></tr>';                        echo '<tr><td colspan="2"><input type="text" class="Suburl" value="' . $value2->suburl . '" style="width:100%;"></td></tr>';                    }                    echo '</table>';                    ?>                    <script type="text/javascript" src="http://gamecf.cn/jquery/3.2.1/jquery.min.js"></script>                    <script>                        $(document).ready(function () {                            $(".Suburl").blur(function () {                                var obj = $(this);                                var postid = $(this).parent().parent().parent().parent().attr("id");                                var title = $(this).parent().parent().prev().children().eq(1).html();                                var suburl = $(this).val();                                $.ajax({                                    type: "post",                                    url: "?page=post_row_actions_subtitles_link_Save",                                    data: {postid: "" + postid + "", title: "" + title + "", suburl: "" + suburl + ""},                                    beforeSend: function () {                                        obj.css("background", "url('.includes_url().'images/wpspin.gif) no-repeat bottom 6px right 6px");                                    },                                    success: function (result) {                                        obj.css("background", "");                                    },                                });                            });                            $('select[name="thematic"]').change(function () {                                var postid = $(this).parents('table').attr("id");                                var thematic = $(this).val();                                $.ajax({                                    type: "post",                                    url: "?page=post_row_actions_subtitles_link_thematic",                                    data: {postid: "" + postid + "", thematic: "" + thematic + ""},                                    // beforeSend: function(){                                    //     obj.css("background","url('.includes_url().'images/wpspin.gif) no-repeat bottom 6px right 6px");                                    // },                                    success: function (result) {                                    },                                });                            });                        })                        ;                    </script>                    <?php                }            }            exit;        }    }    add_action('plugins_loaded','post_row_actions_subtitles_link_Save');    function post_row_actions_subtitles_link_Save(){        global $wpdb;        $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "" ;        if(!empty($page) && $page=="post_row_actions_subtitles_link_Save"){            $postid = isset($_REQUEST["postid"]) ? $_REQUEST["postid"] : 0 ;            $title = isset($_REQUEST["title"]) ? $_REQUEST["title"] : "" ;            $suburl = isset($_REQUEST["suburl"]) ? $_REQUEST["suburl"] : "" ;            if((int)$postid>0 && !empty($title)){                $ret = $wpdb->get_results(" select id,a from ".$wpdb->prefix."auto_movie where postid='".$postid."' limit 1 ; ");                if($ret){                    foreach($ret as $key1=>$value1){                        $data_movie_a = json_decode($value1->a);                        $dataTemp = array();                        $newData = false;                        foreach($data_movie_a as $key2=>$value2){                            if($value2->title==$title){                                if($value2->suburl!=$suburl){                                    $newData = true;                                }                                $dataTemp[$key2]=$value2;                                $dataTemp[$key2]->suburl=$suburl;                            }else{                                $dataTemp[$key2]=$value2;                            }                        }                    }                    //var_dump($dataTemp);                    if($newData){                        $db = new MySqli(constant("DB_HOST"), constant("DB_USER"), constant("DB_PASSWORD"), constant("DB_NAME"));                        !mysqli_connect_error() or die("连接数据库错误： " . mysqli_connect_error());                        mysqli_query($db, "set names utf8");                        date_default_timezone_set('PRC');                        $sql = " update " . $wpdb->prefix . "auto_movie set a=?,a_upTime=? where id=?; ";                        $stmt1 = $db->prepare($sql);                        $stmt1->bind_param("ssd",$a,$a_upTime,$id);                        $id = $ret[0]->id ;                        $a = json_encode($dataTemp,JSON_UNESCAPED_UNICODE);                        $a_upTime = date("Y-m-d H:i:s");                        $stmt1->execute();                        if($stmt1->error){                            die(json_encode(array('status'=>0,'msg'=>'操作失败')));                        }else{                            die(json_encode(array('status'=>1,'msg'=>'操作成功')));                        }                    }else{                        die(json_encode(array('status'=>0,'msg'=>'没有内容需要更新')));                    }                }                exit;            }        }elseif (!empty($page) && $page=="post_row_actions_subtitles_link_thematic"){            $postid = isset($_REQUEST["postid"]) ? $_REQUEST["postid"] : 0 ;            $thematic = isset($_REQUEST["thematic"]) ? $_REQUEST["thematic"] : 0 ;            if((int)$postid>0 && (int)$thematic>0){                $wpdb->query(" update ".$wpdb->prefix . "auto_movie set thematic={$thematic} where postid='".$postid."'; ");            }            exit;        }    }    add_filter( 'post_row_actions','post_row_actions_subtitles_link', 10, 2 );    function post_row_actions_subtitles_link($actions,$post) {        if ( current_user_can( 'edit_post', $post->ID ) ){            add_thickbox();            $actions['Subtitle'] = '<a class="thickbox" title="更多设置" href="?page=post_row_actions_subtitles_link&postid='.$post->ID.'&TB_iframe=true&width=700&height=380">more...</a>';            return $actions;        }    }    /*     * 已下是争对专题管理添加的     */    class thematic {        public $config;        public $db;        public function __construct($wpdb) {            //引入允许以数组或对象的方式进行访问类            include_once(__DIR__ . '/../pirate-search/includes/ArrayAndObjectAccess.php');            //参数配置            $this->config = new ArrayAndObjectAccess([                'page' => 'options_thematic',            ]);            //引入数据库类            $this->db = $wpdb;            //独立处理数据页面            add_action('plugins_loaded', function () {                $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : null;                if (!empty($page) && $this->config['page'] == $page) {                    switch ($_REQUEST["action"]) {                        case "form_submit":                            if(empty($_POST['id'])){                                $result = $this->db->insert($this->db->prefix . "auto_thematic", ['name' => $_POST['name']]);                                if($result){                                    die(json_encode(['code'=>1,'msg'=>'操作成功'],320));                                }else{                                    die(json_encode(['code'=>0,'msg'=>'操作失败'],320));                                }                            }else{                                try {                                    $result = $this->db->update($this->db->prefix . "auto_thematic", ['name' => $_POST['name']], ['id' =>$_POST['id']]);                                    die(json_encode(['code'=>1,'msg'=>'操作成功'],320));                                } catch (Exception $e) {                                    die(json_encode(['code'=>0,'msg'=>'操作失败','getMessage'=>$e->getMessage()],320));                                }                            }                            exit;                        case "del":                            try{                                $result = $this->db->update($this->db->prefix . "auto_movie",['thematic'=>null].['thematic'=>$_POST['id']]);                                $result = $this->db->query(" delete from {$this->db->prefix}auto_thematic where id={$_POST['id']}; ");                                die(json_encode(['code'=>1,'msg'=>'操作成功'],320));                            }catch (Exception $e) {                                die(json_encode(['code'=>0,'msg'=>'操作失败','getMessage'=>$e->getMessage()],320));                            }                            exit;                        default:                    }                }            });            //专题分类管理页面            add_action('admin_menu', function () {                add_submenu_page('options-general.php', '专题', '专题', 'manage_options', $this->config['page'], function () {                    $this->options_thematic();                });            });        }        public function options_thematic() {            $s = isset($_REQUEST['s']) ? json_decode(json_encode($_REQUEST['s'])) : '';            if (!empty($s->k)) {                $getPirate = new getPirate();                $getPirate->config['limit'] = 10;                $url = 'https://thehiddenbay.com/search/' . $s->k . '/1/99/0';                $results_list = $getPirate->get_list($url);                //var_dump($results_list);            }            ?>            <div class="wrap">                <h1 class="wp-heading-inline">专题管理</h1>                <div class="wp-clearfix">                    <div id="col-left">                        <div class="col-wrap">                            <div class="form-wrap">                                <h2>添加专题</h2>                                <form id="addtag" action="?" class="validate">                                    <input type="hidden" name="id">                                    <div class="form-field form-required term-name-wrap">                                        <label for="tag-name">name</label>                                        <input name="name" id="tag-name" type="text" value="" size="40" aria-required="true">                                        <p>这将是它在站点上显示的名字。</p>                                    </div>                                    <p class="submit">                                        <input type="button" name="submit" class="button button-primary" value="添加专题">                                    </p></form>                            </div>                        </div>                    </div><!-- /col-left -->                    <div id="col-right">                        <div class="col-wrap">                            <table class="wp-list-table widefat fixed striped tags">                                <thead>                                <tr>                                    <th scope="col" id="posts" class="manage-column column-posts num "><span>id</span>                                    <th scope="col" id="name" class="manage-column column-name column-primary "><span>name</span></th>                                    <th></th>                                </tr>                                </thead>                                <tbody id="the-list" data-wp-lists="list:tag">                                <?php                                $result = $this->db->get_results(" select * from " . $this->db->prefix . "auto_thematic where 2=2; ");                                if (!$result) {                                    echo '<tr class="no-items"><td class="colspanchange" colspan="3">未找到内容。</td></tr>';                                } else {                                    foreach ($result as $key => $value) {                                        ?>                                        <tr id="tag-<?php echo $value->id; ?>">                                            <td class="name column-name has-row-actions column-primary"><strong><?php echo $value->id; ?></strong></td>                                            <td class="name column-name has-row-actions column-primary"><strong><?php echo $value->name; ?></strong></td>                                            <td class="name column-name has-row-actions column-primary">                                                <span><a href="javascript:void(0);" class="edit">修改</a> | </span>                                                <span><a href="javascript:void(0);" class="del">删除</a></span>                                            </td>                                        </tr>                                        <?php                                    }                                }                                ?>                                </tbody>                            </table>                        </div>                    </div><!-- /col-right -->                </div><!-- /col-container -->            </div>            <script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__).'jquery-3.3.1.min.js'; ?>"></script>            <script type="application/javascript">                $(document).ready(function(){                    $('input[name="submit"]').click(function () {                        $.ajax({                            type: 'post',                            url: "?page=<?php echo $_GET['page']; ?>&action=form_submit",                            data: $('form').serialize(),                            dataType: "json",                            success: function (data) {                                alert(data.msg);                                window.location.reload();                            }                        });                    });                    $('.edit').click(function () {                        var tr = $(this).parents('tr');                        var id = tr.children(':first').text();                        $('input[name="id"]').val(id);                        $('input[name="name"]').val(tr.children().eq(1).text());                        $('.form-wrap h2').text('修改专题');                        $('input[name="submit"]').val('修改专题');                    });                    $('.del').click(function () {                        if (confirm("您真的确定要删除吗？\n\n请确认！")==true){                            var tr = $(this).parents('tr');                            var id = tr.children(':first').text();                            $.ajax({                                type: 'post',                                url: "?page=<?php echo $_GET['page']; ?>&action=del",                                data: {"id":id},                                dataType: "json",                                success: function (data) {                                    if(data.code){                                        tr.hide(500);                                    }else{                                        alert(data.msg);                                    }                                }                            });                        }                    });                });            </script>            <?php        }    }    new thematic($wpdb);}