<?php
session_start();
$_SESSION['user_name'] = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : false;
if (!$_SESSION['user_name']) {
    exit("登录用户后才可以编辑博客");
}

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/db.php';
$db = new Db();
$id = $_GET['id'];
$cates = $db->table('cates')->select();
$item = $db->table('article')->where(['id'=>$id])->select()[0];
$contents = htmlspecialchars_decode($item['contents']);
//print_r($item);
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
</head>
<body>
    <form class="form-horizontal">
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">博客题目</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="title" value="<?php echo $item['title'];?>">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">博客分类</label>
            <div class="col-sm-10">
                <select class="form-control" id="art-cate">
                    <?php foreach($cates as $val){?>
                        <option value="<?php echo $val['id']?>" <?php echo ($item['cid']==$val['id']?'selected':'');?>><?php echo $val['title']?></option>
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
                <button  type="button" class="btn btn-lg btn-primary center-block" onclick="save()" style="width: 200px">保存</button>
            </div>
        </div>
    </form>

</body>
</html>
<script>
    let E = window.wangEditor;
    let editor = new E('#editor');
    function create_editor() {
        editor.create();
    }
    create_editor();
    editor.txt.html('<?php echo $contents;?>');

    function save() {
        let id = <?php echo $id;?>;
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
        $.post('/service/update_article.php',{title:title,cate:cate,msg:msg,add_time:add_time,id:id},function (data) {
            if (data.code > 0){
                UI.alert({msg:data.msg,img:'error'});
            }else{
                UI.alert({msg:data.msg,img:'ok'});
                setTimeout(function () {
                    window.location.reload()
                },1500)
            }
        },'json');
    };
</script>