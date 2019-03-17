<?php
$_POST['title'] = isset($_POST['title']) ? $_POST['title'] : false;
$_POST['cate'] = isset($_POST['cate']) ? $_POST['cate'] : false;
$_POST['msg'] = isset($_POST['msg']) ? $_POST['msg'] : false;
$_POST['add_time'] = isset($_POST['add_time']) ? $_POST['add_time'] : false;
$title = htmlspecialchars(trim($_POST['title']));
$cate = htmlspecialchars(trim($_POST['cate']));
$content = htmlspecialchars(trim($_POST['msg']));
$add_time = htmlspecialchars(trim($_POST['add_time']));

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/db.php';
$db = new Db();
$res_id = $db->table('article')->insert(['title'=>$title,'cid'=>$cate,'contents'=>$content,'add_time'=>$add_time]);
if ($res_id > 0){
    exit(json_encode(['code'=>0,'msg'=>'保存成功']));
}else{
    exit(json_encode(['code'=>1,'msg'=>'保存失败']));
}