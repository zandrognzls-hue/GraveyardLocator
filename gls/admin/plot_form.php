<?php require __DIR__.'/../includes/bootstrap.php';require_admin();
$id=(int)($_GET['id']??0);$p=$id?q('SELECT * FROM plots WHERE id=?',[$id])->fetch():null;if($id&&!$p)go('admin/plots.php');
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(isset($_POST['delete'])&&$p){q('DELETE FROM plots WHERE id=?',[$id]);flash('Plot deleted.');go('admin/plots.php');}
  $v=[trim($_POST['section']),(int)$_POST['row_no'],trim($_POST['plot_no']),$_POST['status'],$_POST['tier']];
  try{if($p)q('UPDATE plots SET section=?,row_no=?,plot_no=?,status=?,tier=? WHERE id=?',[...$v,$id]);else q('INSERT INTO plots(section,row_no,plot_no,status,tier) VALUES(?,?,?,?,?)',$v);
    log_activity('Plot saved: '.$v[0].'-'.$v[2]);flash('Plot saved.');go('admin/plots.php');}
  catch(PDOException $e){flash('That section, row and plot number already exists.','err');}}
$f=$p??['section'=>'','row_no'=>'','plot_no'=>'','status'=>'available','tier'=>'Regular'];
admin_header($p?'Edit Plot':'Add New Plot','plots');?>
<div class="card"><div class="head"><h1><?=$p?'Edit Plot':'Add New Plot'?></h1></div><form method="post"><?=csrf_field()?><div class="grid2">
<div><label>Section</label><input name="section" value="<?=h($f['section'])?>" required></div><div><label>Row</label><input type="number" name="row_no" value="<?=h($f['row_no'])?>" required></div>
<div><label>Plot number</label><input name="plot_no" value="<?=h($f['plot_no'])?>" required></div>
<div><label>Status</label><select name="status"><?php foreach(['available','reserved','occupied'] as $s):?><option value="<?=$s?>" <?=$f['status']===$s?'selected':''?>><?=ucfirst($s)?></option><?php endforeach;?></select></div>
<div><label>Price tier</label><select name="tier"><?php foreach(['Regular','Prime','Super Prime'] as $s):?><option <?=$f['tier']===$s?'selected':''?>><?=$s?></option><?php endforeach;?></select></div></div>
<p><button class="btn"><i class="fas fa-save"></i> Save plot</button> <a class="btn gr" href="plots.php">Cancel</a> <?php if($p):?><button class="btn rd" name="delete" value="1" onclick="return confirm('Delete this plot?')">Delete plot</button><?php endif;?></p></form></div>
<?php admin_footer();
