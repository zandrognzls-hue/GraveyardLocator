<?php require __DIR__.'/../includes/bootstrap.php';require_admin();
$c=fn($w)=>q("SELECT COUNT(*) FROM plots WHERE status=?",[$w])->fetchColumn();
$tg=q('SELECT COUNT(*) FROM graves')->fetchColumn();
admin_header('Dashboard','index');?>
<div class="head card" style="padding:25px 28px"><h1>Dashboard</h1><div style="display:flex;gap:14px;align-items:center"><span class="btn" style="border-radius:50%;width:50px;height:50px;justify-content:center;font-size:22px">A</span><div><b style="font-size:20px"><?=h($_SESSION['name'])?></b><br><small>Administrator</small></div></div></div>
<div class="stats">
<?php foreach([['Total Graves',$tg,'#2196f3','fa-monument'],['Available Plots',$c('available'),'#4caf50','fa-check'],['Reserved Plots',$c('reserved'),'#ff9800','fa-clock'],['Occupied Plots',$c('occupied'),'#f44336','fa-times']] as $s):?>
<div class="stat"><div class="ic" style="background:<?=$s[2]?>"><i class="fas <?=$s[3]?>"></i></div><p><?=$s[0]?></p><b><?=$s[1]?></b></div><?php endforeach;?></div>
<div class="card"><h2>Quick Actions</h2><hr style="border:0;border-top:1px solid #eee;margin:18px 0 22px"><div class="qa">
<a href="grave_form.php"><i class="fas fa-plus-circle"></i><b>Add New Grave</b><span>Register a new grave record</span></a>
<a href="plots.php"><i class="fas fa-edit"></i><b>Update Plot Status</b><span>Change availability of plots</span></a>
<a href="reservations.php"><i class="fas fa-calendar-check"></i><b>Manage Reservations</b><span>View and process requests</span></a>
<a href="report.php"><i class="fas fa-chart-bar"></i><b>Generate Reports</b><span>Create cemetery reports</span></a></div></div>
<div class="card"><h2>Recent Activities</h2><hr style="border:0;border-top:1px solid #eee;margin:18px 0">
<?php foreach(q('SELECT * FROM activities ORDER BY id DESC LIMIT 8') as $a):?><p><b><?=h($a['message'])?></b><br><small style="color:#888"><?=date('M j, Y g:i A',strtotime($a['created_at']))?></small></p><?php endforeach;?></div>
<?php admin_footer();
