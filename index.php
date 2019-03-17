<?php
session_start();
$username = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
require_once $_SERVER['DOCUMENT_ROOT'].'/lib/db.php';
$db = new Db();
//获取博客分类
$cates = $db->table('cates')->select();
//获取分页数据
$path = '/index.php';
$page_size = 4;
$page = max(1,isset($_GET['page']) ? $_GET['page'] : 1);
$cid = isset($_GET['cid'])?$_GET['cid']:0;
$where = [];
if ($cid){
    $where['cid'] = $cid;
    $path = "/index.php?cid=$cid";
}
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : false;
$keyword = htmlspecialchars($keyword);
if($keyword){
    $where['title'] = "like %$keyword%";
    $path = "/index.php?keyword=$keyword";
}
$symbol = "?";
if (strpos($path,'?')){
    $symbol = '&';
}
$order = isset($_GET['order']) ? $_GET['order'] : 'add_time desc';

if($order){
    $path .= $symbol.'order='.$order;
}
//$path .= $symbol.$order;
$pages = $db->table('article')->field('id,cid,title,pv,add_time')->where($where)->order($order)->pages($page, $page_size, $path);
$pages_data = $pages['data'];
//echo "<pre>";
//var_dump($_SERVER);
//exit;

//print_r($pages_data);
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
    <script src="static/js/util.js"></script>
</head>
<body>
<nav class="navbar navbar-default">
  <div class="container">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="/index.php">我的博客</a>
    </div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="navbar-collapse-1">
      <form class="navbar-form navbar-left" method="get" action="<?php echo $_SERVER['PHP_SELF']?>">
        <div class="form-group">
        	<input type="text" class="form-control" placeholder="搜索博客" NAME="keyword" id="keyword" value="<?php if($keyword){echo $keyword;}?>" required>
          	<button class="btn btn-default" id="search" type="submit">搜索</button>
        </div>
      </form>
      <ul class="nav navbar-nav navbar-right">
          <?php if($username){?>
              <li><a><?php echo $username;?></a></li>
              <li><a class="btn" onclick="logout()" id="logout">退出</a></li>
          <?php }else{?>
              <li><a class="btn" onclick="login()">登录</a></li>
          <?php }?>
          <li><a class="btn" onclick="manage_article()" >博客管理</a></li>
          <li><a class="btn" onclick="add_article()" >发布博客</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
<div class="container main">
    <div class="row">
        <div class="col-lg-3 content-left">
            <p class="cate">博客分类</p>
            <div class="list-group">
                <?php foreach($cates as $value){?>
                    <a href="/index.php?cid=<?php echo $value['id']?>" class="list-group-item <?php if($cid==$value['id']){echo 'active';}?>">
                        <?php echo $value['title']?>
                    </a>
                <?php }?>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="nav">
                <a href="<?php $path=explode('order',$path);$path[1]='=pv desc';$path=implode('order',$path);echo $path;?>" <?php if($order == 'pv desc'){echo "class='active disabled'";}?>>热门</a>
                <a href="<?php $path=explode('order',$path);$path[1]='=add_time desc';$path=implode('order',$path);echo $path;?>" <?php if($order == 'add_time desc'){echo "class='active disabled'";}?>>最新</a>
            </div>
            <?php if($pages_data){?>
                <?php foreach ($pages_data as $key=>$val){?>
                    <div class="content-list">
                        <div class="content-item ">
                            <img src="/static/image/avatar.png" alt="">
                            <div class="content-title">
                                <p><a href="javascript:;" onclick="show_detail(<?php echo $val['id'];?>)"><?php echo $val['title']?></a></p>
                                <p><span><?php echo $val['pv']?></span>次浏览&nbsp;<span> <?php echo date('Y-m-d H:i:s',$val['add_time'])?></span></p>
                            </div>
                        </div>
                    </div>
                <?php }?>
            <?php }else{?>
                <div class="content-list">
                    <div class="content-item">暂无数据</div>
                </div>
            <?php }?>
            <div class="pages">
                <nav aria-label="...">
                    <?php echo $pages['pages'];?>
                </nav>
            </div>
        </div>
    </div>

</div>

</body>
</html>
<script>
   // UI.alert({msg:'登录成功',img:'ok'});
   function login () {
       UI.open({title:'登陆',url: '/login.php'});
   }
   function logout() {
       // UI.alert({msg:'确定退出？',img:'warning'});
       $.get('/service/logout.php',{},function (data) {
           if (data.code>0){
               UI.alert({msg:data.msg,img:'error'});
           }else{
               UI.alert({msg:data.msg,img:'ok'});
               setTimeout(function(){window.location.reload();},1000);
           }
       },'json')
   }
   //发布博客
    function add_article() {
        UI.open({title: "发布博客",url: "/add_article.php",width: 1000,height:500});
    }
    //管理博客
   function manage_article(){
       UI.open({title: "管理博客",url: "/manage_article.php",width:1200, height:700})
   }
    $('.active').removeAttr('href'); // 防止重复提交，去掉href属性
   //博客详情
    function show_detail(id){
        $('.col-lg-9').empty();//清空右侧内容
        //加载博客详情
         console.log($(event.target).text());
        $('.col-lg-9').load('/detail.php',{id:id})
    }
</script>