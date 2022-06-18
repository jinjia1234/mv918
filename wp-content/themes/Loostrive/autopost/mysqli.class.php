<?php

class mysqli {
    public function __construct() {
        $this->db = new MySqli(constant("DB_HOST"), constant("DB_USER"), constant("DB_PASSWORD"), constant("DB_NAME"));
        !mysqli_connect_error() or die("连接数据库错误： " . mysqli_connect_error());
        mysqli_query($this->db, "set names utf8");
    }
}