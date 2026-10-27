<?php require __DIR__.'/../includes/bootstrap.php';require_user();
$rs=q('SELECT r.*,p.section,p.row_no,p.plot_no FROM reservations r JOIN plots p ON p.id=r.plot_id WHERE r.user_id=? ORDER BY r.id DESC',[$_SESSION['uid']])->fetchAll();
site_header('Dashboard','dashboard');?>
<div class="wrap"><?php alerts();?><h1><i class="fas fa-tachometer-alt"></i> User Dashboard</h1><p style="color:#666;font-size:19px">Welcome back, <?=h($_SESSION['name'])?>!</p>
<div class="banner"><h2><i class="fas fa-monument"></i> Cemetery Reservation System</h2><p style="margin:12px 0 0;font-size:19px">Browse available burial plots, make reservations, and search for loved ones all in one place.</p></div>
<div class="card"><div class="head"><h2><i class="fas fa-list"></i> Your Reservation Requests</h2><a class="btn pv" href="reserve.php"><i class="fas fa-plus"></i> Make New Reservation</a></div>
<?php if(!$rs):?><div class="empty"><i class="fas fa-inbox"></i><b>No Reservations Yet</b><p>You haven't made any reservation requests. Browse our cemetery map to find and reserve a plot.</p><a class="btn pv" style="padding:18px 30px" href="<?=url('public/map.php')?>"><i class="fas fa-map-marked-alt"></i> Browse Cemetery Map</a></div>
<?php else:?><table><tr><th>Plot</th><th>Requested</th><th>Status</th></tr><?php foreach($rs as $r):?><tr><td><?=h(plot_label($r))?></td><td><?=fdate($r['created_at'])?></td><td><span class="pill <?=$r['status']?>"><?=ucfirst($r['status'])?></span></td></tr><?php endforeach;?></table><?php endif;?></div></div>
<?php site_footer();
