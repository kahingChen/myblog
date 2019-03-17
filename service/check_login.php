<?php
//接受数据
$username = htmlspecialchars(trim($_POST['username'],' '));
$password = htmlspecialchars(trim($_POST['password'],' '));

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/db.php';
$db = new Db();
$user = $db->table('user')->where(['username'=>$username])->select();
$user_name = isset($user[0]['username']) ? $user[0]['username'] : '';

if ($username !== $user_name){
    exit(json_encode(['code'=>1,'msg'=>'用户名不存在']));
}
if (sha1($password) !== $user[0]['password']){
    exit(json_encode(['code'=>1, 'msg'=>'密码错误']));
}else{
    //开启session
    session_start();
    $_SESSION['user_name'] = $username;
    exit(json_encode(['code'=>'0','msg'=>'登陆成功,正在跳转...']));
}