<?php
header("Content-type:text/html;charset=utf-8");
$stime = microtime(true);
include_once('functions.php');
?>
<html>
<head>

    <meta name="referrer" content="no-referrer"/>
    <meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <style>
        span {
            padding: 0px 8px;
            background: #DDDDDD;
            color: #FFFFFF;
            margin-right: 10px;
            -moz-border-radius: 5px;
            -webkit-border-radius: 9px;
            -moz-box-shadow: 0 1px 3px rgba(0, 0, 0, .5);
            /* -webkit-box-shadow: 0 1px 3px rgba(0,0,0,.5); */
            text-shadow: 0 -1px 1px rgba(255, 255, 255, 0.25);
        }

        img {
            vertical-align: bottom;
        }
    </style>
</head>

<body>
<?php

$db = new SQLite3('eventv2.db');
$groupid = I("get.groupid");
if (!empty($groupid)) {
    //$results=$db->exec("attach 'D:/Program Files/酷Q/酷Q Air CQA-图灵机器人(小灰灰)/data/3483414790/cache.db' as cache");
    //$sql = " select [event].*,(select `nick` from [cache].[stranger] where qq=substr([event].`account`,9,20))nick  from [event] where `group`='qq/group/".$groupid."' or `account`='qq/group/".$groupid."' order by `id` desc limit 100; ";
    $sql = " select `type`,`tag`,`account`,`operator`,`content`,`time` from [event] where `group`='qq/group/" . $groupid . "' or `account`='qq/group/" . $groupid . "' order by `id` desc limit 2000; ";
    $results = $db->query($sql);
    $i = 0;
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $i++;
        $user = substr($row['account'], 8);
        switch ($row['type']) {
            case '26':
                $user = '&nbsp;&nbsp;<font color="#999">' . substr($row['operator'], 8) . '邀请' . $user . '加入群</font>';
                break;
            case '27':
                $user = '&nbsp;&nbsp;<font color="#999">' . $user . '离开群</font>';
                break;
            case '2':
                $user = '&nbsp;&nbsp;<font color="red">' . $user . '</font>';
                break;
            default:
                null;
        }
        $content = $row['content'];
        // dump($content);
        $content = textLinks($content);
        $content = textImages($content);
        $content = textFaceAt($content);
        $content = $row['tag'] == '' ? '<font color="#007F3F">' . $content . '</font>' : $content;
        $text = '<span>' . date('Y-m-d H:i:s', $row['time']) . '</span>' . $user . PHP_EOL . $content . PHP_EOL;
        echo str_replace(array(PHP_EOL), '<br>' . PHP_EOL, $text);

    }
    echo <<<script
<script>
function imgerror(img){
    img.src='./images/face/space.gif';
    img.onerror=null;
}
</script>
script;
    echo '<br>' . PHP_EOL . '<center>当前页面执行时间为 ' . (float)(microtime(true) - $stime) . ' 秒</center>';
    exit;
}

?>
<!DOCTYPE html>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<link rel="stylesheet" type="text/css" href="http://download.lanrenmb.com/xin/wap/050903//css/hui.css"/>
</head>
<body>
<div class="hui-wrap">
    <div class="hui-list" style="margin-top:0px;">

        <?php
        $results = $db->query(" select substr(`group`,10)groupid,count(id)count,time from [event] where `type`=2 and `group` like 'qq/group/%' group by `group` order by time desc limit 100; ");
        $i = 0;
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            ?>
            <a href="?groupid=<?php echo $row['groupid']; ?>">
                <div class="hui-list-icons" style="margin:2px 0;width:initial;">
                    <img src="http://p.qlogo.cn/gh/<?php echo $row['groupid']; ?>/<?php echo $row['groupid']; ?>/140" style="height:46px;width:inherit;border-radius: 25px;"/>
                </div>
                <div class="hui-list-text">
                    <?php echo $row['groupid']; ?>
                    <div class="hui-list-info">
                        <?php echo date("Y-m-d H:i:s", $row['time']); ?>
                        <span class="hui-icons"><?php echo $row['count']; ?></span>
                    </div>
                </div>
            </a>
            <?php
            //echo '<img src="http://p.qlogo.cn/gh/'.$row['groupid'].'/'.$row['groupid'].'/140">'.$row['groupid'].'(<a href="?groupid='.$row['groupid'].'">'.$row['count'].'</a>)<br>';
            $i++;
        }
        echo '<br>' . PHP_EOL . '<center>当前页面执行时间为 ' . (float)(microtime(true) - $stime) . ' 秒</center>';
        ?>
    </div>
    <script src="http://download.lanrenmb.com/xin/wap/050903//js/hui.js" type="text/javascript" charset="utf-8"></script>

</body>
</html>