<?php
session_start();
$_SESSION['user_name'] = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : false;
if (!$_SESSION['user_name']) {
    exit("登录用户后才可以发布博客");
}

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/db.php';
$db = new Db();
$data = $db->table('cates')->select();
//print_r($data);exit;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>发布博客</title>
    <link rel="stylesheet" href="/static/css/site.css">
    <link rel="stylesheet" href="/static/plugins/bootstrap/css/bootstrap.css">
    <script src="/static/plugins/jquery.js"></script>
    <script src="/static/plugins/bootstrap/js/bootstrap.js"></script>
    <script src="static/js/util.js"></script>
    <script src="/static/plugins/wangEditor.min.js"></script>
    <style>
        .container{width: 95%}
    </style>
</head>
<body>
<div class="container">
    <form class="form-horizontal">
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">博客题目</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="title" placeholder="请输入博客题目">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">博客分类</label>
            <div class="col-sm-10">
                <select class="form-control" id="art-cate">
                    <?php foreach($data as $val){?>
                    <option value="<?php echo $val['id']?>"><?php echo $val['title']?></option>
                    <?php }?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">博客内容</label>
            <div class="col-sm-10">
                <div id="editor"></div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <button  type="button" class="btn btn-primary btn-lg center-block" id="save">保存</button>
            </div>
        </div>
    </form>
</div>
</body>
</html>
<script>
    let E = window.wangEditor;
    let editor = new E('#editor');
    function create_editor() {
        editor.create();
    }
    create_editor();
    //接受判断数据
    $('#save').click(function () {
        let title = $('#title').val();
        let cate = $('#art-cate').val();
        let msg = editor.txt.html();
        if(title.length === 0){
            UI.alert({msg:'博客题目不能为空',img:'warning'});
            return;
        }
        if(msg == '' || msg =='<p><br></p>'){
            UI.alert({msg:'博客内容不能为空',img:'warning'});
            return;
        }
        let add_time = "<?php echo time()?>";
        $.post('/service/save_article.php',{title:title,cate:cate,msg:msg,add_time:add_time},function (data) {
            if (data.code > 0){
                UI.alert({msg:data.msg,img:'error'});
            }else{
                UI.alert({msg:data.msg,img:'ok'});
                setTimeout(function () {
                    window.location.reload()
                },1500)
            }
        },'json');
    });

</script>