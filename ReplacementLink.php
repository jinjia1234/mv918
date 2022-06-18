<?php
header("Content-type:text/html;charset=utf-8");
#设置执行时间不限时
set_time_limit(0);

exit;

include_once "./wp-config.php";

$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
if (mysqli_connect_errno()) {
    printf("连接数据库出错 %s\n", mysqli_connect_error());
    exit();
}
if (!$mysqli->set_charset("utf8")) {
    printf("加载字符集UTF8错误: %s\n", $mysqli->error);
}

$query = "SELECT * FROM ".$table_prefix."auto_movie WHERE id>=2100 and id<2153 ORDER by ID ASC LIMIT 0,1000";
if ($result = $mysqli->query($query)) {
    while ($row = $result->fetch_object()) {
        $a = json_decode($row->a);
        foreach ($a as $key=>$value){
            $download = explode("&tr=",$value->download);
            $a[$key]->download = $download[0]."&tr=udp%3A%2F%2Ftracker.coppersurfer.tk%3A6969&tr=udp%3A%2F%2Ftracker.leechers-paradise.org%3A6969&tr=udp%3A%2F%2Ftracker.opentrackr.org%3A1337%2Fannounce&tr=udp%3A%2F%2Ftorrent.gresille.org%3A80%2Fannounce&tr=udp%3A%2F%2F9.rarbg.me%3A2710%2Fannounce";
        }
        $query = "update ".$table_prefix."auto_movie set a='".json_encode($a)."' where id='".$row->id."'; ";
        if(!$mysqli->query($query)){
            printf("更新数据发生错误：",$mysqli->error);
        }else{
            echo str_repeat(" ",4194304);   //默认缓存区大小4194304
            var_dump($row->id.'成功');
            echo "<br>";
            flush();
        }

    }
}
$result->close();