<?php require __DIR__.'/../includes/bootstrap.php';require_admin();
function process($ids,$act){
  foreach($ids as $i){$r=q('SELECT * FROM reservations WHERE id=? AND status="pending"',[(int)$i])->fetch();if(!$r)continue;
    q('UPDATE reservations SET status=?,processed_at=NOW() WHERE id=?',[$act==='approve'?'approved':'rejected',$r['id']]);
    q('UPDATE plots SET status=? WHERE id=? AND status<>"occupied"',[$act==='approve'?'reserved':'available',$r['plot_id']]);
    log_activity('Reservation '.($act==='approve'?'approved':'rejected').': '.$r['name']);}}
if($_SERVER['REQUEST_METHOD']==='POST'){
  $act=$_POST['action']??'';if(in_array($act,['approve','reject'],true)){process($_POST['ids']??[],$act);flash('Reservations updated.');}go('admin/reservations.php');}
$st=$_GET['status']??'';$s=trim($_GET['q']??'');$from=$_GET['from']??'';$to=$_GET['to']??'';
$w=['1'];$a=[];
if($st){$w[]='r.status=?';$a[]=$st;}if($s){$w[]='(r.name LIKE ? OR r.email LIKE ? OR r.phone LIKE ?)';array_push($a,"%$s%","%$s%","%$s%");}
if($from){$w[]='DATE(r.created_at)>=?';$a[]=$from;}if($to){$w[]='DATE(r.created_at)<=?';$a[]=$to;}
$rows=q('SELECT r.*,p.section,p.row_no,p.plot_no FROM reservations r JOIN plots p ON p.id=r.plot_id WHERE '.implode(' AND ',$w).' ORDER BY r.id DESC',$a)->fetchAll();
if(isset($_GET['export'])){header('Content-Type: text/csv');header('Content-Disposition: attachment; filename=reservations.csv');$o=fopen('php://output','w');
  fputcsv($o,['Name','Email','Phone','Section','Row','Plot','Status','Requested']);foreach($rows as $r)fputcsv($o,[$r['name'],$r['email'],$r['phone'],$r['section'],$r['row_no'],$r['plot_no'],$r['status'],$r['created_at']]);exit;}
$cnt=q('SELECT status,COUNT(*) c FROM reservations GROUP BY status')->fetchAll(PDO::FETCH_KEY_PAIR);
admin_header('Reservations','reservations');?>
<div class="stats"><?php foreach([['Total Requests',array_sum($cnt),'#2196f3','fa-clipboard'],['Pending',$cnt['pending']??0,'#ff9800','fa-clock'],['Approved',$cnt['approved']??0,'#4caf50','fa-check'],['Rejected',$cnt['rejected']??0,'#f44336','fa-times']] as $x):?>
<div class="stat"><div class="ic" style="background:<?=$x[2]?>"><i class="fas <?=$x[3]?>"></i></div><p><?=$x[0]?></p><b><?=$x[1]?></b></div><?php endforeach;?></div>
<div class="card"><h2><i class="fas fa-filter"></i> Filter Reservations</h2><form class="row" style="margin-top:14px"><div><label>Status</label><select name="status"><option value="">All</option><?php foreach(['pending','approved','rejected'] as $o):?><option value="<?=$o?>" <?=$st===$o?'selected':''?>><?=ucfirst($o)?></option><?php endforeach;?></select></div>
<div><label>Search</label><input name="q" value="<?=h($s)?>" placeholder="Name, email, or phone"></div><div><label>From Date</label><input type="date" name="from" value="<?=h($from)?>"></div><div><label>To Date</label><input type="date" name="to" value="<?=h($to)?>"></div>
<div style="flex-basis:100%;display:flex;gap:12px"><button class="btn"><i class="fas fa-search"></i> Apply Filters</button><a class="btn gr" href="reservations.php"><i class="fas fa-redo"></i> Reset</a><a class="btn or" href="?<?=h(http_build_query($_GET))?>&export=1"><i class="fas fa-file-export"></i> Export CSV</a></div></form></div>
<div class="card"><h1 style="font-size:36px;margin-bottom:20px">Reservation Requests (<?=count($rows)?> total)</h1>
<form method="post"><?=csrf_field()?><div class="row" style="background:#f7f7f7;padding:14px;margin-bottom:12px;justify-content:flex-start;flex-wrap:nowrap"><label style="margin:0;flex:none"><input type="checkbox" style="width:auto" onclick="document.querySelectorAll('.ck').forEach(c=>c.checked=this.checked)"> Select All</label>
<select name="action" style="max-width:220px"><option value="">-- Bulk Actions --</option><option value="approve">Approve</option><option value="reject">Reject</option></select><button class="btn"><i class="fas fa-check"></i> Apply</button></div>
<table><tr><th></th><th>Name</th><th>Contact</th><th>Plot</th><th>Request Date</th><th>Status</th><th>Actions</th></tr>
<?php foreach($rows as $r):?><tr><td><?php if($r['status']==='pending'):?><input class="ck" type="checkbox" name="ids[]" value="<?=$r['id']?>" style="width:auto"><?php endif;?></td><td><b><?=h($r['name'])?></b></td>
<td><i class="fas fa-envelope"></i> <?=h($r['email'])?><br><i class="fas fa-phone"></i> <?=h($r['phone'])?></td><td><b>Section <?=h($r['section'])?></b><br><small>Row <?=$r['row_no']?>, Plot <?=h($r['plot_no'])?></small></td>
<td><?=fdate($r['created_at'])?><br><small><?=date('h:i A',strtotime($r['created_at']))?></small></td><td><span class="pill <?=$r['status']?>"><?=ucfirst($r['status'])?></span></td>
<td><details><summary class="btn sm bl" style="list-style:none"><i class="fas fa-eye"></i> View</summary><p style="max-width:240px"><?=h($r['message']?:'No message.')?></p></details>
<?php if($r['status']==='pending'):?><button class="btn sm" name="action" value="approve" onclick="this.form.querySelectorAll('.ck').forEach(c=>c.checked=false);this.form.insertAdjacentHTML('beforeend','<input type=hidden name=ids[] value=<?=$r['id']?>>')">Approve</button>
<button class="btn sm rd" name="action" value="reject" onclick="this.form.querySelectorAll('.ck').forEach(c=>c.checked=false);this.form.insertAdjacentHTML('beforeend','<input type=hidden name=ids[] value=<?=$r['id']?>>')">Reject</button><?php else:?><em style="color:#999">Processed</em><?php endif;?></td></tr><?php endforeach;?>
<?php if(!$rows):?><tr><td colspan="7" class="empty">No reservation requests.</td></tr><?php endif;?></table></form></div>
<?php admin_footer();
