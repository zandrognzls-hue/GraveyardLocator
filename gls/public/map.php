<?php require __DIR__.'/../includes/bootstrap.php';
$plots=q('SELECT p.id,p.section,p.row_no,p.plot_no,p.status,p.tier,p.map_polygon,g.id gid,CONCAT(g.first_name," ",g.last_name) occupant FROM plots p LEFT JOIN graves g ON g.plot_id=p.id ORDER BY p.section,p.plot_no')->fetchAll();
$focus=0;if(!empty($_GET['grave']))$focus=(int)q('SELECT plot_id FROM graves WHERE id=?',[(int)$_GET['grave']])->fetchColumn();if(!empty($_GET['plot']))$focus=(int)$_GET['plot'];
site_header('Cemetery Map','map');?>
<div class="wrap"><h1 style="margin-bottom:18px">Cemetery Map</h1><div class="mapside"><div class="card" style="padding:15px"><div class="mapbox" id="map"></div></div>
<div><div class="card"><h3>Legend</h3><p><span class="dot available"></span>Available &nbsp; <span class="dot reserved"></span>Reserved &nbsp; <span class="dot occupied"></span>Occupied</p><label for="pick">Find a plot</label><select id="pick"><option value="">Select a plot</option><?php foreach($plots as $p):?><option value="<?=$p['id']?>" <?=$focus==$p['id']?'selected':''?>><?=h($p['section'].' - '.$p['plot_no'])?></option><?php endforeach;?></select></div>
<div class="card" id="info"><p style="color:#777">Select a plot on the map or from the list to see its details. Plots that have not been mapped yet only appear in the list.</p></div></div></div></div>
<script src="<?=url('assets/js/map.js')?>"></script><script>
const plots=<?=json_encode($plots,JSON_HEX_TAG)?>,$=i=>document.getElementById(i),logged=<?=json_encode(!empty($_SESSION['uid']))?>;
function show(p){if(!p)return;map.hl=p.id;map.render();
  $('info').innerHTML=`<h3>Section ${p.section}, Row ${p.row_no}, Plot ${p.plot_no}</h3><p><span class="dot ${p.status}"></span>${p.status[0].toUpperCase()+p.status.slice(1)} &middot; ${p.tier}</p>`+
  (p.occupant?`<p>Occupant: <b>${p.occupant}</b></p><a class="btn sm bl" href="grave.php?id=${p.gid}">View record</a>`:'')+
  (p.status==='available'?`<p><a class="btn" href="<?=url('user/reserve.php')?>?plot=${p.id}"><i class="fas fa-calendar-plus"></i> ${logged?'Reserve this plot':'Log in to reserve'}</a></p>`:'')+
  (p.map_polygon?'':'<p style="color:#777">This plot has not been placed on the map yet.</p>')}
const map=new CemMap($('map'),{img:<?=json_encode(url('assets/img/cemetery-map.png'))?>,plots,onPlot:show});
$('pick').onchange=()=>{const p=plots.find(x=>x.id==$('pick').value);if(p){map.focus(p.id);show(p)}};
<?php if($focus):?>setTimeout(()=>{const p=plots.find(x=>x.id==<?=$focus?>);if(p){map.focus(p.id);show(p)}},50);<?php endif;?>
</script><?php site_footer();
