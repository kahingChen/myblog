<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>登陆博客</title>
    <link rel="stylesheet" href="/static/css/site.css">
    <link rel="stylesheet" href="/static/css/login.css">
    <link rel="stylesheet" href="/static/plugins/bootstrap/css/bootstrap.css">
    <script src="/static/plugins/jquery.js"></script>
    <script src="/static/plugins/bootstrap/js/bootstrap.js"></script>
    <script src="static/js/util.js"></script>
</head>
<body>
<div class="container-fluid">
    <div class="title text-center"><h2>登陆博客</h2></div>
    <div class="input-group">
        <label class="input-group-addon" for="username"><span class="glyphicon glyphicon-user"></span></label>
        <input type="text" class="form-control" id="username" placeholder="请输入用户名" >
    </div><br>
    <div class="input-group">
        <label class="input-group-addon " for="password"><span class="glyphicon glyphicon-lock"></span></label>
        <input type="password" class="form-control" id="password" placeholder="请输入密码">
    </div>
    <button type="button" class="btn btn-primary btn-lg center-block" onclick="login()" id="submit">登陆</button>
</div>
</body>
</html>
<script>
    function login(){
        let username = $('#username').val();
        let password = $('#password').val();
        if (username.length == 0){
            UI.alert({msg:'用户名不能为空',img:'warning'});
        }
        if (password.length == 0){
            UI.alert({msg:'密码不能为空',img:'warning'});
        }
        console.log(username);
        $.post('/service/check_login.php',{username:username,password:password},function (data) {
            console.log(username);
            if (data.code>0){
                UI.alert({msg:data.msg,img:'warning'});
            }else{
                UI.alert({msg:data.msg,img:'ok'});
                setTimeout(function () {
                    parent.window.location.href = '/index.php';
                },1000)
            }
        },'json')
    }

    $(document).keydown(function(event){
        if(event.keyCode ===13){
            $("#submit").trigger("click");
        }
    });

</script>