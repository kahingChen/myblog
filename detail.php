<?php
$id = $_POST['id'];
require_once $_SERVER['DOCUMENT_ROOT'].'/lib/db.php';
$db = new Db();
$data = $db->table('article')->where(['id'=>$id])->select();
$article = $data[0];
//浏览量加1
$pv = $data[0]['pv']+1;
$db->table('article')->where(['id'=>$id])->update(['pv'=>$pv]);
//echo "<pre>";
//print_r($data);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>我的博客</title>
	<link rel="stylesheet" href="/static/css/site.css">
	<link rel="stylesheet" href="/static/plugins/bootstrap/css/bootstrap.css">
	<script src="/static/plugins/jquery.js"></script>
	<script src="/static/plugins/bootstrap/js/bootstrap.js"></script>
</head>
<body>


		<div class="art-title">
			<b><?php echo $article['title'];?><b>
		</div>
		<div class="art-time"><?php echo date("Y-m-d H:i:s",$article['add_time'])?></div>
		<div class="art-detail"><?php echo htmlspecialchars_decode($article['contents'])?></div>


</body>
</html>
