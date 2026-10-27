<?php require __DIR__.'/../includes/bootstrap.php';require_admin();
if($_SERVER['REQUEST_METHOD']==='POST'){
  $st=$_POST['status']??'';
  if(in_array($st,['available','reserved','occupied'],true)){
    $ids=array_map('intval',$_POST['ids']??(isset($_POST['one'])?[$_POST['one']]:[]));
    if($ids){q('UPDATE plots SET status=? WHERE id IN ('.implode(',',array_fill(0,count($ids),'?')).')',[$st,...$ids]);log_activity(count($ids).' plot(s) set to '.$st);flash('Plot status updated.');}}
  go('admin/plots.php');}
$sec=$_GET['section']??'';$sta=$_GET['status']??'';
$w=[];$a=[];if($sec!==''){$w[]='p.section=?';$a[]=$sec;}if($sta!==''){$w[]='p.status=?';$a[]=$sta;}
$rows=q('SELECT p.*,g.id gid,CONCAT(g.first_name," ",g.last_name) occ FROM plots p LEFT JOIN graves g ON g.plot_id=p.id'.($w?' WHERE '.implode(' AND ',$w):'').' ORDER BY p.section,p.row_no,p.plot_no',$a)->fetchAll();
admin_header('Plot Management','plots');?>
<div class="card"><div class="head"><h1>Plot Management</h1><a class="btn" href="plot_form.php"><i class="fas fa-plus"></i> Add New Plot</a></div>
<form class="row" style="margin-bottom:20px;flex-wrap:nowrap;justify-content:flex-start"><select name="section" style="max-width:200px"><option value="">All Sections</option><?php foreach(q('SELECT DISTINCT section FROM plots ORDER BY 1')->fetchAll(PDO::FETCH_COLUMN) as $s):?><option <?=$sec===$s?'selected':''?>><?=h($s)?></option><?php endforeach;?></select>
<select name="status" style="max-width:150px"><option value="">All Statuses</option><?php foreach(['available','reserved','occupied'] as $s):?><option value="<?=$s?>" <?=$sta===$s?'selected':''?>><?=ucfirst($s)?></option><?php endforeach;?></select><button class="btn">Filter</button></form>
<form method="post" id="bulk"><?=csrf_field()?><div class="row" style="margin-bottom:14px;justify-content:flex-start;flex-wrap:nowrap"><select name="status" style="max-width:220px"><option value="available">Mark selected as Available</option><option value="reserved">Mark selected as Reserved</option><option value="occupied">Mark selected as Occupied</option></select><button class="btn">Apply to selected</button></div>
<table><tr><th><input type="checkbox" style="width:auto" onclick="document.querySelectorAll('.ck').forEach(c=>c.checked=this.checked)"></th><th>Section</th><th>Row</th><th>Plot</th><th>Status</th><th>Occupant</th><th>Actions</th></tr>
<?php foreach($rows as $r):?><tr><td><input class="ck" type="checkbox" name="ids[]" value="<?=$r['id']?>" style="width:auto"></td><td><?=h($r['section'])?></td><td><?=$r['row_no']?></td><td><?=h($r['plot_no'])?></td><td><span class="dot <?=$r['status']?>"></span><?=ucfirst($r['status'])?></td><td><?=h($r['occ']?:'—')?></td>
<td style="white-space:nowrap"><?php if($r['gid']):?><a class="btn sm bl" href="<?=url('public/grave.php?id='.$r['gid'])?>"><i class="fas fa-eye"></i> View</a> <?php elseif($r['status']==='available'):?><button class="btn sm bl" form="r<?=$r['id']?>"><i class="fas fa-bookmark"></i> Reserve</button> <?php endif;?><a class="btn sm or" href="plot_form.php?id=<?=$r['id']?>"><i class="fas fa-edit"></i> Edit</a></td></tr><?php endforeach;?></table></form>
<?php foreach($rows as $r)if($r['status']==='available'):?><form method="post" id="r<?=$r['id']?>"><?=csrf_field()?><input type="hidden" name="one" value="<?=$r['id']?>"><input type="hidden" name="status" value="reserved"></form><?php endif;?></div>
<?php admin_footer();
