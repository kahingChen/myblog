<?php
session_start();
$_SESSION['user_name'] = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : false;
if (!$_SESSION['user_name']) {
    exit("登录用户后才可以管理博客");
}
require_once $_SERVER['DOCUMENT_ROOT'].'/lib/db.php';
$db = new Db();
$path = '';
$page_size = 4;
$page = max(1,isset($_GET['page']) ? $_GET['page'] : 1);
$pages = $db->table('article')->field('id,cid,title,add_time')->order('add_time desc')->pages($page, $page_size,$path);
//echo "<pre>";
//var_dump($pages);
$cates = $db->table('cates')->select();
//exit(var_dump($cates));
$data = $pages['data'];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="/static/plugins/bootstrap/css/bootstrap.css">
    <script src="/static/plugins/jquery.js"></script>
    <script src="/static/plugins/bootstrap/js/bootstrap.js"></script>
    <script src="static/js/util.js"></script>
</head>
<body>
<a href="javascript:location.replace(location.href);" class="btn btn-primary" style="float:right;margin-bottom: 10px">刷新</a>

    <table class="table table-condensed table-hover text-center">
        <thead>
        <tr class="info">
            <td class="text-center">id</td>
            <td>分类</td>
            <td>标题</td>
            <td>创建时间</td>
            <td>操作</td>
        </tr>
        </thead>
        <tbody>

            <?php foreach($data as $item){?>
            <tr>
            <th scope="row" class="text-center"><?php echo $item['id'];?></th>
            <td><?php echo $cates[$item['cid']-1]['title'];?></td>
            <td><?php echo $item['title'];?></td>
            <td><?php echo date('Y-m-d  h:i:s',$item['add_time']);?></td>
            <td>
                <a href="javascript:;" onclick="edit(<?php echo $item['id'];?>)"><span class="glyphicon glyphicon-edit"></span></a>&nbsp;&nbsp;
                <a href="javascript:;" onclick="del(this,<?php echo $item['id'];?>)"><span class="glyphicon glyphicon-trash"></span></a>
            </td>
            </tr>
            <?php }?>

        </tbody>
    </table>

    <div class="pages" style="float: right">
        <nav aria-label="...">
            <?php echo $pages['pages'];?>
        </nav>
    </div>
    <script>
        function edit(id){
            console.log(id);
            UI.open({title: "编辑博客",url: "/edit_article.php?id="+id,width: 1100,height:600});
        }
        function del(obj,id) {
            //异步提交要删除的id
            $.get('/del_article.php',{id:id},
                function (res) {
                    if(res.status === 0){
                        UI.alert({msg:'删除成功',img:'ok'})
                        $(obj).parents('tr').remove();
                    }else {
                        UI.alert({msg:'删除失败',img:'error'})
                    }
                },'json'
            )
        }
    </script>
</body>
</html>

