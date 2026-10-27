<?php require __DIR__.'/../includes/bootstrap.php';check_csrf();
$u=q('SELECT * FROM users WHERE email=?',[trim($_POST['email']??'')])->fetch();
if($u&&password_verify($_POST['password']??'',$u['password'])){session_regenerate_id(true);$_SESSION['uid']=$u['id'];$_SESSION['role']=$u['role'];$_SESSION['name']=$u['full_name'];go($u['role']==='admin'?'admin/index.php':'user/dashboard.php');}
go('public/index.php?login=1&err=1');
