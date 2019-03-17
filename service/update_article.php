<?php
//判断提交数据 是否存在且有值
$_POST['title'] = isset($_POST['title']) ? $_POST['title'] : false;
$_POST['cate'] = isset($_POST['cate']) ? $_POST['cate'] : false;
$_POST['msg'] = isset($_POST['msg']) ? $_POST['msg'] : false;
$_POST['add_time'] = isset($_POST['add_time']) ? $_POST['add_time'] : false;

$id = $_POST['id'];
$title = htmlspecialchars(trim($_POST['title']));
$cate = htmlspecialchars(trim($_POST['cate']));
$content = htmlspecialchars(trim($_POST['msg']));
$add_time = htmlspecialchars(trim($_POST['add_time']));

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/db.php';
$db = new Db();
//更新记录
$res_id = $db->table('article')->where(['id'=>$id])->update(['title'=>$title,'cid'=>$cate,'contents'=>$content,'add_time'=>$add_time]);
if ($res_id > 0){
    exit(json_encode(['code'=>0,'msg'=>'更新成功']));
}else{
    exit(json_encode(['code'=>1,'msg'=>'更新失败']));
}