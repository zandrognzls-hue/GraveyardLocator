<?php
require __DIR__.'/../config.php';
session_start();
function db(){static $p;if(!$p){$p=new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',DB_USER,DB_PASS,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);}return $p;}
function q($sql,$a=[]){$s=db()->prepare($sql);$s->execute($a);return $s;}
function h($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function url($p=''){return BASE_URL.'/'.ltrim($p,'/');}
function go($p){header('Location: '.url($p));exit;}
function csrf(){if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(16));return $_SESSION['csrf'];}
function csrf_field(){return '<input type="hidden" name="csrf" value="'.csrf().'">';}
function check_csrf(){if($_SERVER['REQUEST_METHOD']==='POST'&&!hash_equals($_SESSION['csrf']??'',$_POST['csrf']??'')){http_response_code(400);exit('Invalid request token. Go back and reload the page.');}}
function flash($m=null,$t='ok'){if($m!==null){$_SESSION['flash']=[$m,$t];return;}$f=$_SESSION['flash']??null;unset($_SESSION['flash']);return $f;}
function setting($k,$d=''){static $c;if($c===null)$c=q('SELECT k,v FROM settings')->fetchAll(PDO::FETCH_KEY_PAIR);return $c[$k]??$d;}
function require_admin(){if(($_SESSION['role']??'')!=='admin')go('admin/login.php');check_csrf();}
function require_user(){if(empty($_SESSION['uid']))go('public/index.php?login=1');check_csrf();}
function log_activity($t){q('INSERT INTO activities(message)VALUES(?)',[$t]);}
function fdate($d){return $d?date('M j, Y',strtotime($d)):'—';}
function plot_label($p){return 'Section '.$p['section'].', Row '.$p['row_no'].', Plot '.$p['plot_no'];}
function alerts(){if($f=flash())echo '<div class="alert '.($f[1]==='err'?'err':'ok').'">'.h($f[0]).'</div>';}
function admin_header($title,$active){
  $n=['index'=>['Dashboard','fa-tachometer-alt'],'graves'=>['Grave Records','fa-monument'],'plots'=>['Plot Management','fa-th'],'reservations'=>['Reservations','fa-calendar-check'],'users'=>['Users','fa-users'],'profile'=>['My Profile','fa-user-circle'],'map_coordinate_tool'=>['Cemetery Map','fa-map-marked-alt'],'report'=>['Reports','fa-chart-bar'],'settings'=>['Settings','fa-cog'],'logout'=>['Logout','fa-sign-out-alt']];
  ?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=h($title)?> | <?=h(APP_NAME)?></title>
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"><link rel="stylesheet" href="<?=url('assets/css/style.css')?>"></head><body>
  <aside class="side"><div class="brand">Cemetery Admin<small>Management Portal</small></div><nav><?php foreach($n as $f=>$i):?><a href="<?=url("admin/$f.php")?>" class="<?=$active===$f?'on':''?>"><i class="fas <?=$i[1]?>"></i><?=$i[0]?></a><?php endforeach;?></nav></aside>
  <main class="main"><?php alerts();
}
function admin_footer(){echo '</main></body></html>';}
function site_header($title,$active=''){
  $u=!empty($_SESSION['uid']);
  $n=$u?['dashboard'=>['user/dashboard.php','Dashboard','fa-tachometer-alt'],'map'=>['public/map.php','Map','fa-map-marked-alt'],'history'=>['user/history.php','History','fa-history'],'price'=>['public/pricelist.php','Price List','fa-tags'],'reserve'=>['user/reserve.php','Reserve','fa-calendar-plus']]:['home'=>['public/index.php','Home','fa-home'],'map'=>['public/map.php','Map','fa-map-marked-alt'],'history'=>['user/history.php','History','fa-history'],'price'=>['public/pricelist.php','Price List','fa-tags']];
  ?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=h($title)?> | <?=h(APP_NAME)?></title>
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"><link rel="stylesheet" href="<?=url('assets/css/style.css')?>"></head><body>
  <header class="top"><div class="in"><a class="logo" href="<?=url('public/index.php')?>"><?php if(!$u):?><svg viewBox="0 0 60 62" fill="none" stroke="#4caf50" stroke-width="3"><ellipse cx="30" cy="31" rx="26" ry="28"/><path d="M30 12v38M20 24h20" /></svg><?php endif;?>HOLY CROSS GARDEN</a>
  <nav><?php foreach($n as $k=>$i):?><a href="<?=url($i[0])?>" class="<?=$active===$k?'on':''?>"><i class="fas <?=$i[2]?>"></i><?=$i[1]?></a><?php endforeach;?>
  <?php if($u):?><a class="out" href="<?=url('public/logout.php')?>"><i class="fas fa-sign-out-alt"></i>Logout</a><?php else:?><a href="#" onclick="document.getElementById('lm').classList.add('show');return false"><i class="fas fa-sign-in-alt"></i>Login</a><?php endif;?></nav></div></header><?php
}
function site_footer(){?>
  <footer class="foot"><div class="in"><div><h3><?=h(setting('name'))?> Memorial Park</h3><p>Preserving memories and providing peace since 1950</p></div><div><h3>Quick Links</h3><a href="<?=url('public/index.php')?>">Home</a><a href="<?=url('user/dashboard.php')?>">Dashboard</a><a href="<?=url('public/map.php')?>">Cemetery Map</a></div><div><h3>Contact Us</h3><p><i class="fas fa-map-marker-alt"></i> <?=h(setting('address'))?></p><p><i class="fas fa-phone"></i> <?=h(setting('phone'))?></p><p><i class="fas fa-envelope"></i> <?=h(setting('email'))?></p></div></div></footer>
  <?php if(empty($_SESSION['uid'])):?><div class="modal <?=isset($_GET['login'])?'show':''?>" id="lm"><div class="box"><div style="display:flex;justify-content:space-between"><h2 style="margin:0 auto">Login to Your Account</h2><a href="#" onclick="document.getElementById('lm').classList.remove('show');return false" style="color:#999;text-decoration:none">&#10006;</a></div>
  <?php if(isset($_GET['err'])):?><div class="alert err" style="margin-top:12px">Incorrect email or password.</div><?php endif;?>
  <form method="post" action="<?=url('public/login.php')?>"><?=csrf_field()?><label>Email:</label><input type="email" name="email" required><label>Password:</label><input type="password" name="password" required><p><button class="btn" style="padding:14px 22px;font-size:17px">Login</button></p><p style="text-align:center">Don't have an account? <a href="<?=url('public/register.php')?>">Register here</a></p></form></div></div><?php endif;?>
  </body></html><?php }
