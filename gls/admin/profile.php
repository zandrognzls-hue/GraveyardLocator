<?php require __DIR__.'/../includes/bootstrap.php';require_admin();
$me=q('SELECT * FROM users WHERE id=?',[$_SESSION['uid']])->fetch();
if($_SERVER['REQUEST_METHOD']==='POST'){
  $pw=(string)($_POST['password']??'');
  $fullName=trim((string)($_POST['full_name'] ?? ''));
  $email=trim((string)($_POST['email'] ?? ''));
  if($pw!==''&&strlen($pw)<(int)setting('min_password',8))flash('Password is too short.','err');
  else try{
    q('UPDATE users SET full_name=?,email=? WHERE id=?',[$fullName,$email,$me['id']]);
    if($pw!=='')q('UPDATE users SET password=? WHERE id=?',[password_hash($pw,PASSWORD_DEFAULT),$me['id']]);
    $_SESSION['name']=$fullName;flash('Profile updated.');go('admin/profile.php');}catch(PDOException $e){flash('That email is already in use.','err');}}
admin_header('My Profile','profile');
?>
<div class="card" style="max-width:1000px;margin:auto"><h1 style="text-align:center">My Profile</h1><div style="width:188px;height:188px;border-radius:50%;background:var(--g);color:#fff;font-size:80px;display:grid;place-items:center;margin:15px auto 30px"><?=h(strtoupper($me['username'][0]))?></div>
<div style="background:#e3f2fd;border-left:4px solid var(--bl);padding:20px;border-radius:4px"><b style="color:var(--bl);font-size:20px"><i class="fas fa-info-circle"></i> Account Information</b><p>User ID: <?=$me['id']?><br>Username: <?=h($me['username'])?><br>Role: <b style="color:var(--g)">Administrator</b><br>Member Since: <?=fdate($me['created_at'])?></p></div>
<h3 style="text-align:center;margin:30px 0 0;font-weight:500">Personal Information</h3><form method="post"><?=csrf_field()?><label>Full Name *</label><input name="full_name" value="<?=h($me['full_name'])?>" required><label>Email Address *</label><input type="email" name="email" value="<?=h($me['email'])?>" required><label>New Password (leave blank to keep)</label><input type="password" name="password" autocomplete="new-password"><p><button class="btn"><i class="fas fa-save"></i> Update profile</button></p></form></div>
<?php admin_footer();
