<?php require __DIR__.'/../includes/bootstrap.php';require_admin();
$id=(int)($_GET['id']??0);$g=$id?q('SELECT * FROM graves WHERE id=?',[$id])->fetch():null;if($id&&!$g)go('admin/graves.php');
if($_SERVER['REQUEST_METHOD']==='POST'){
  $d=fn($k)=>($_POST[$k]??'')?:null;$pid=(int)($_POST['plot_id']??0)?:null;
  if(!trim($_POST['first_name']??'')||!trim($_POST['last_name']??'')){flash('First and last name are required.','err');}
  else{
    $v=[trim($_POST['first_name']),trim($_POST['last_name']),$d('birth_date'),$d('death_date'),$d('interment_date'),$pid,trim($_POST['notes']??'')];
    if($g){q('UPDATE graves SET first_name=?,last_name=?,birth_date=?,death_date=?,interment_date=?,plot_id=?,notes=? WHERE id=?',[...$v,$id]);
      if($g['plot_id']&&$g['plot_id']!=$pid)q("UPDATE plots SET status='available' WHERE id=?",[$g['plot_id']]);}
    else q('INSERT INTO graves(first_name,last_name,birth_date,death_date,interment_date,plot_id,notes) VALUES(?,?,?,?,?,?,?)',$v);
    if($pid)q("UPDATE plots SET status='occupied' WHERE id=?",[$pid]);
    log_activity(($g?'Grave updated: ':'New grave added: ').$v[0].' '.$v[1]);flash('Grave record saved.');go('admin/graves.php');}}
$f=$g??$_POST+['first_name'=>'','last_name'=>'','birth_date'=>'','death_date'=>'','interment_date'=>'','plot_id'=>'','notes'=>''];
$plots=q("SELECT * FROM plots WHERE status<>'occupied' OR id=? ORDER BY section,row_no,plot_no",[$g['plot_id']??0])->fetchAll();
admin_header($g?'Edit Grave':'Add New Grave','graves');?>
<div class="card"><div class="head"><h1><?=$g?'Edit Grave':'Add New Grave'?></h1></div>
<form method="post"><?=csrf_field()?><div class="grid2"><div><label>First name *</label><input name="first_name" value="<?=h($f['first_name'])?>" required></div><div><label>Last name *</label><input name="last_name" value="<?=h($f['last_name'])?>" required></div>
<div><label>Date of birth</label><input type="date" name="birth_date" value="<?=h($f['birth_date'])?>"></div><div><label>Date of death</label><input type="date" name="death_date" value="<?=h($f['death_date'])?>"></div>
<div><label>Interment date</label><input type="date" name="interment_date" value="<?=h($f['interment_date'])?>"></div>
<div><label>Plot</label><select name="plot_id"><option value="">-- No plot --</option><?php foreach($plots as $p):?><option value="<?=$p['id']?>" <?=$f['plot_id']==$p['id']?'selected':''?>><?=h(plot_label($p).' ('.$p['status'].')')?></option><?php endforeach;?></select></div></div>
<label>Notes</label><textarea name="notes" rows="3"><?=h($f['notes'])?></textarea><p><button class="btn"><i class="fas fa-save"></i> Save grave</button> <a class="btn gr" href="graves.php">Cancel</a></p></form></div>
<?php admin_footer();
