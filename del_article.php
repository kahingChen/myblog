<?php
session_start();
$_SESSION['user_name'] = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : false;
if (!$_SESSION['user_name']) {
    exit("登录用户后才可以操作");
}

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/db.php';
$db = new Db();
//接受要删除的id
$id = $_GET['id'];

$res = $db->table('article')->where(['id'=>$id])->delete();
if($res){
    exit(json_encode(['status'=>0]));
}
exit(json_encode(['status'=>2]));
