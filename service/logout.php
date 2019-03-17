<?php
session_start();
if (!isset($_SESSION['user_name'])){exit;}
$_SESSION['user_name'] = null;
exit(json_encode(['code'=>0,'msg'=>'退出登录成功']));
//header("location: /index.php");
