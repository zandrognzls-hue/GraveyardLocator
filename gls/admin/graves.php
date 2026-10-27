<?php require __DIR__.'/../includes/bootstrap.php';require_admin();
if(isset($_POST['delete'])){$g=q('SELECT plot_id FROM graves WHERE id=?',[$_POST['delete']])->fetch();
  if($g){q('DELETE FROM graves WHERE id=?',[$_POST['delete']]);if($g['plot_id'])q("UPDATE plots SET status='available' WHERE id=?",[$g['plot_id']]);log_activity('Grave record deleted');flash('Grave record deleted.');}go('admin/graves.php');}
$s=trim($_GET['q']??'');
$rows=q('SELECT g.*,p.section,p.row_no,p.plot_no FROM graves g LEFT JOIN plots p ON p.id=g.plot_id WHERE CONCAT(g.first_name," ",g.last_name) LIKE ? ORDER BY g.id',["%$s%"])->fetchAll();
admin_header('Grave Records','graves');?>
<div class="card"><div class="head"><h1>Grave Records</h1><a class="btn" href="grave_form.php"><i class="fas fa-plus"></i> Add New Grave</a></div>
<form class="row" style="margin-bottom:22px;gap:0"><input name="q" value="<?=h($s)?>" placeholder="Search by name..." style="border-radius:4px 0 0 4px"><button class="btn" style="border-radius:0 4px 4px 0;padding:13px 18px"><i class="fas fa-search"></i></button></form>
<table><tr><th>Name</th><th>Dates</th><th>Plot Location</th><th>Actions</th></tr>
<?php foreach($rows as $r):?><tr><td><?=h($r['first_name'].' '.$r['last_name'])?></td><td><?=fdate($r['birth_date'])?> - <?=fdate($r['death_date'])?></td><td><?=$r['plot_no']?h(plot_label($r)):'—'?></td>
<td style="white-space:nowrap"><a class="btn sm bl" href="<?=url('public/grave.php?id='.$r['id'])?>"><i class="fas fa-eye"></i> View</a> <a class="btn sm or" href="grave_form.php?id=<?=$r['id']?>"><i class="fas fa-edit"></i> Edit</a>
<form method="post" style="display:inline" onsubmit="return confirm('Delete this grave record?')"><?=csrf_field()?><button class="btn sm rd" name="delete" value="<?=$r['id']?>"><i class="fas fa-trash"></i> Delete</button></form></td></tr><?php endforeach;?>
<?php if(!$rows):?><tr><td colspan="4" class="empty">No records found.</td></tr><?php endif;?></table></div>
<?php admin_footer();
