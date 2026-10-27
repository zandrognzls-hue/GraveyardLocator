<?php require __DIR__.'/../includes/bootstrap.php';
$s=trim($_GET['q']??'');$rows=[];
if($s!==''){$l="%$s%";
  $rows=q('SELECT g.*,p.section,p.row_no,p.plot_no FROM graves g LEFT JOIN plots p ON p.id=g.plot_id WHERE CONCAT(g.first_name," ",g.last_name) LIKE ? OR p.plot_no LIKE ? OR p.section LIKE ? OR g.birth_date LIKE ? OR g.death_date LIKE ? OR DATE_FORMAT(g.death_date,"%M %Y") LIKE ? OR DATE_FORMAT(g.birth_date,"%M %Y") LIKE ? ORDER BY g.last_name',[$l,$l,$l,$l,$l,$l,$l])->fetchAll();
  if(!empty($_SESSION['uid']))q('INSERT INTO search_history(user_id,query) VALUES(?,?)',[$_SESSION['uid'],mb_substr($s,0,255)]);}
site_header('Search');?>
<div class="wrap"><h1 style="margin-bottom:18px">Search Cemetery Records</h1><form class="sbox" style="margin:0 0 25px;max-width:none"><input name="q" value="<?=h($s)?>" placeholder="Name, date, plot number, or section"><button class="btn"><i class="fas fa-search"></i> Search</button></form>
<div class="card"><?php if($s===''):?><div class="empty"><i class="fas fa-search"></i>Enter a name, date, plot number, or section.</div>
<?php elseif(!$rows):?><div class="empty"><i class="fas fa-search"></i>No records match "<?=h($s)?>". Try a shorter name or a plot number.</div>
<?php else:?><table><tr><th>Name</th><th>Dates</th><th>Plot Location</th><th></th></tr><?php foreach($rows as $r):?><tr><td><?=h($r['first_name'].' '.$r['last_name'])?></td><td><?=fdate($r['birth_date'])?> - <?=fdate($r['death_date'])?></td><td><?=$r['plot_no']?h(plot_label($r)):'—'?></td>
<td style="white-space:nowrap"><a class="btn sm bl" href="grave.php?id=<?=$r['id']?>"><i class="fas fa-eye"></i> View</a> <a class="btn sm" href="map.php?grave=<?=$r['id']?>"><i class="fas fa-map-marker-alt"></i> Locate</a></td></tr><?php endforeach;?></table><?php endif;?></div></div>
<?php site_footer();
