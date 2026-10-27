<?php require __DIR__.'/../includes/bootstrap.php';require_user();$u=$_SESSION['uid'];
$v=q('SELECT g.id,g.first_name,g.last_name,MAX(vg.viewed_at) t FROM viewed_graves vg JOIN graves g ON g.id=vg.grave_id WHERE vg.user_id=? GROUP BY g.id,g.first_name,g.last_name ORDER BY t DESC LIMIT 10',[$u])->fetchAll();
$s=q('SELECT query,MAX(created_at) t FROM search_history WHERE user_id=? GROUP BY query ORDER BY t DESC LIMIT 10',[$u])->fetchAll();
site_header('History','history');?>
<div class="wrap"><h1 style="text-align:center">Search History</h1><p style="text-align:center;font-size:19px">Review your recently viewed graves and searches</p><div class="grid2" style="gap:35px;margin-top:35px">
<div class="card" style="min-height:400px"><h2 style="color:#2e7d32"><i class="fas fa-eye" style="color:var(--g)"></i> Recently Viewed Graves</h2>
<?php if(!$v):?><div class="empty"><i class="fas fa-eye-slash"></i>No recently viewed graves<p><a class="btn" href="<?=url('public/search.php')?>">Start Searching</a></p></div><?php else:foreach($v as $r):?><p><a href="<?=url('public/grave.php?id='.$r['id'])?>"><?=h($r['first_name'].' '.$r['last_name'])?></a> <small style="color:#888"><?=fdate($r['t'])?></small></p><?php endforeach;endif;?></div>
<div class="card"><h2 style="color:#2e7d32"><i class="fas fa-search" style="color:var(--g)"></i> Recent Searches</h2>
<?php if(!$s):?><div class="empty"><i class="fas fa-search"></i>No recent searches<p><a class="btn" href="<?=url('public/search.php')?>">Search Cemetery Records</a></p></div><?php else:foreach($s as $r):?><p><a href="<?=url('public/search.php?q='.urlencode($r['query']))?>"><?=h($r['query'])?></a> <small style="color:#888"><?=fdate($r['t'])?></small></p><?php endforeach;endif;?></div></div></div>
<?php site_footer();
