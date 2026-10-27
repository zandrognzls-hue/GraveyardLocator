<?php require __DIR__.'/../includes/bootstrap.php';
$err='';$f=['full_name'=>'','username'=>'','email'=>''];
if($_SERVER['REQUEST_METHOD']==='POST'){check_csrf();$f=array_map('trim',array_intersect_key($_POST,$f));$min=(int)setting('min_password',8);
  if(strlen($_POST['password']??'')<$min)$err="Password must be at least $min characters.";
  elseif(($_POST['password']??'')!==($_POST['confirm']??''))$err='Passwords do not match.';
  else try{q('INSERT INTO users(username,full_name,email,password) VALUES(?,?,?,?)',[$f['username'],$f['full_name'],$f['email'],password_hash($_POST['password'],PASSWORD_DEFAULT)]);
    $_SESSION['uid']=db()->lastInsertId();$_SESSION['role']='user';$_SESSION['name']=$f['full_name'];go('user/dashboard.php');}
  catch(PDOException $e){$err='That username or email is already registered.';}}
site_header('Register');?>
<div class="wrap" style="max-width:620px"><div class="card"><h1>Create an account</h1><?php if($err):?><div class="alert err" style="margin-top:14px"><?=h($err)?></div><?php endif;?>
<form method="post"><?=csrf_field()?><label>Full name</label><input name="full_name" value="<?=h($f['full_name'])?>" required><label>Username</label><input name="username" value="<?=h($f['username'])?>" required><label>Email</label><input type="email" name="email" value="<?=h($f['email'])?>" required><label>Password</label><input type="password" name="password" required><label>Confirm password</label><input type="password" name="confirm" required><p><button class="btn">Create account</button></p></form></div></div>
<?php site_footer();
