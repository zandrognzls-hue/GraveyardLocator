<?php require __DIR__.'/../includes/bootstrap.php';
$g=q('SELECT g.*,p.section,p.row_no,p.plot_no,p.status FROM graves g LEFT JOIN plots p ON p.id=g.plot_id WHERE g.id=?',[(int)($_GET['id']??0)])->fetch();if(!$g)go('public/search.php');
if(!empty($_SESSION['uid']))q('INSERT INTO viewed_graves(user_id,grave_id) VALUES(?,?)',[$_SESSION['uid'],$g['id']]);
site_header('Grave details');?>
<div class="wrap" style="max-width:760px"><div class="card"><h1><?=h($g['first_name'].' '.$g['last_name'])?></h1>
<p style="font-size:20px"><?=fdate($g['birth_date'])?> &ndash; <?=fdate($g['death_date'])?></p>
<table><tr><th>Plot location</th><td><?=$g['plot_no']?h(plot_label($g)):'Not assigned'?></td></tr><tr><th>Interment date</th><td><?=fdate($g['interment_date'])?></td></tr><?php if($g['notes']):?><tr><th>Notes</th><td><?=nl2br(h($g['notes']))?></td></tr><?php endif;?></table>
<p><a class="btn" href="map.php?grave=<?=$g['id']?>"><i class="fas fa-map-marker-alt"></i> Locate on map</a> <a class="btn gr" href="search.php">Back to search</a></p></div></div>
<?php site_footer();
