<?php require __DIR__.'/../includes/bootstrap.php';require_admin();
$tab=$_GET['tab']??'general';
function dump(){$o="-- Backup ".date('c')."\nSET FOREIGN_KEY_CHECKS=0;\n";
  foreach(['users','plots','graves','reservations','search_history','viewed_graves','settings','activities'] as $t){$o.="DELETE FROM `$t`;\n";
    foreach(db()->query("SELECT * FROM `$t`") as $r){$o.="INSERT INTO `$t` (`".implode('`,`',array_keys($r))."`) VALUES (".implode(',',array_map(fn($v)=>$v===null?'NULL':db()->quote($v),$r)).");\n";}}
  return $o."SET FOREIGN_KEY_CHECKS=1;\n";}
if(isset($_GET['download'])){header('Content-Type: application/sql');header('Content-Disposition: attachment; filename=backup-'.date('Ymd-His').'.sql');echo dump();exit;}
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(($_POST['form']??'')==='general'){foreach(['name','email','phone','address'] as $k)q('REPLACE INTO settings(k,v) VALUES(?,?)',[$k,trim($_POST[$k]??'')]);flash('Settings saved.');go('admin/settings.php');}
  if(($_POST['form']??'')==='security'){q('REPLACE INTO settings(k,v) VALUES("min_password",?)',[max(6,(int)$_POST['min_password'])]);flash('Security settings saved.');go('admin/settings.php?tab=security');}
  if(($_POST['form']??'')==='restore'&&is_uploaded_file($_FILES['sql']['tmp_name']??'')){
    try{db()->exec(file_get_contents($_FILES['sql']['tmp_name']));flash('Backup restored.');}catch(Exception $e){flash('Restore failed: '.$e->getMessage(),'err');}go('admin/settings.php?tab=backup');}}
admin_header('Settings','settings');?>
<div class="card"><div class="head"><h1>System Settings</h1></div><div class="tabs"><?php foreach(['general'=>'General Settings','security'=>'Security','backup'=>'Backup & Restore'] as $k=>$l):?><a href="?tab=<?=$k?>" class="<?=$tab===$k?'on':''?>"><?=$l?></a><?php endforeach;?></div>
<?php if($tab==='general'):?><h2>General Settings</h2><p>Configure basic information about your cemetery website.</p><form method="post" style="max-width:760px"><?=csrf_field()?><input type="hidden" name="form" value="general">
<label>Cemetery Name:</label><input name="name" value="<?=h(setting('name'))?>"><label>Contact Email:</label><input type="email" name="email" value="<?=h(setting('email'))?>"><label>Contact Phone:</label><input name="phone" value="<?=h(setting('phone'))?>"><label>Cemetery Address:</label><textarea name="address" rows="3"><?=h(setting('address'))?></textarea><p><button class="btn"><i class="fas fa-save"></i> Save Settings</button></p></form>
<?php elseif($tab==='security'):?><h2>Security</h2><form method="post" style="max-width:500px"><?=csrf_field()?><input type="hidden" name="form" value="security"><label>Minimum password length</label><input type="number" min="6" name="min_password" value="<?=h(setting('min_password',8))?>"><p><button class="btn">Save</button></p></form>
<?php else:?><h2>Backup & Restore</h2><p><a class="btn pu" href="?download=1"><i class="fas fa-download"></i> Download SQL backup</a></p><form method="post" enctype="multipart/form-data" onsubmit="return confirm('Restoring replaces all current data. Continue?')"><?=csrf_field()?><input type="hidden" name="form" value="restore"><label>Restore from a backup file (.sql)</label><input type="file" name="sql" accept=".sql" required style="max-width:400px"><p><button class="btn or"><i class="fas fa-upload"></i> Restore backup</button></p></form><?php endif;?></div>
<?php admin_footer();
