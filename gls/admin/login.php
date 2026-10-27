<?php require __DIR__.'/../includes/bootstrap.php';
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){check_csrf();
  $u=q('SELECT * FROM users WHERE username=? AND role="admin"',[trim($_POST['username']??'')])->fetch();
  if($u&&password_verify($_POST['password']??'',$u['password'])){session_regenerate_id(true);$_SESSION['uid']=$u['id'];$_SESSION['role']='admin';$_SESSION['name']=$u['full_name'];go('admin/index.php');}
  $err='Incorrect username or password.';}
?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin Login | <?=h(APP_NAME)?></title>
<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"><link rel="stylesheet" href="<?=url('assets/css/style.css')?>"></head>
<body><div class="login-wrap"><div class="login"><h1>Admin Portal</h1><p style="font-size:19px"><?=h(APP_NAME)?></p>
<?php if($err):?><div class="alert err"><?=h($err)?></div><?php endif;?>
<form method="post"><?=csrf_field()?><label>Username</label><input name="username" required autofocus><label>Password</label><input type="password" name="password" required><p><button class="btn" style="width:100%;justify-content:center;padding:16px;font-size:18px"><i class="fas fa-sign-in-alt"></i> Login</button></p></form>
<a href="<?=url('public/index.php')?>"><i class="fas fa-arrow-left"></i> Return to Cemetery Website</a></div></div></body></html>
