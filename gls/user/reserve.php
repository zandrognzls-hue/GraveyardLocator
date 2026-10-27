<?php require __DIR__.'/../includes/bootstrap.php';require_user();
$me=q('SELECT * FROM users WHERE id=?',[$_SESSION['uid']])->fetch();
if($_SERVER['REQUEST_METHOD']==='POST'){
  $p=q("SELECT * FROM plots WHERE id=? AND status='available'",[(int)$_POST['plot_id']])->fetch();
  if(!$p||!trim($_POST['phone']??''))flash('Choose an available plot and enter a phone number.','err');
  else{q('INSERT INTO reservations(user_id,plot_id,name,email,phone,message) VALUES(?,?,?,?,?,?)',[$me['id'],$p['id'],trim($_POST['name']),trim($_POST['email']),trim($_POST['phone']),trim($_POST['message']??'')]);
    log_activity('New reservation request from '.trim($_POST['name']));flash('Reservation request sent. We will review it shortly.');go('user/dashboard.php');}}
$plots=q("SELECT * FROM plots WHERE status='available' ORDER BY section,row_no,plot_no")->fetchAll();$sel=(int)($_GET['plot']??0);
site_header('Reserve','reserve');?>
<div class="wrap" style="max-width:760px"><?php alerts();?><div class="card"><h1><i class="fas fa-calendar-plus"></i> Reserve a Plot</h1><form method="post"><?=csrf_field()?>
<label>Plot</label><select name="plot_id" required><option value="">Select an available plot</option><?php foreach($plots as $p):?><option value="<?=$p['id']?>" <?=$sel==$p['id']?'selected':''?>><?=h(plot_label($p).' - '.$p['tier'])?></option><?php endforeach;?></select>
<label>Full name</label><input name="name" value="<?=h($me['full_name'])?>" required><label>Email</label><input type="email" name="email" value="<?=h($me['email'])?>" required><label>Phone</label><input name="phone" required><label>Message (optional)</label><textarea name="message" rows="3"></textarea>
<p><button class="btn pv">Send reservation request</button> <a class="btn gr" href="<?=url('public/pricelist.php')?>">View price list</a></p></form></div></div>
<?php site_footer();
