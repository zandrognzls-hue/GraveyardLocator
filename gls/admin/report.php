<?php require __DIR__.'/../includes/bootstrap.php';require_admin();
$c=fn($w)=>(int)q("SELECT COUNT(*) FROM plots WHERE status=?",[$w])->fetchColumn();
$yr=q('SELECT YEAR(COALESCE(interment_date,created_at)) y,COUNT(*) c FROM graves GROUP BY y')->fetchAll(PDO::FETCH_KEY_PAIR);
$labels=range((int)date('Y')-9,(int)date('Y'));$vals=array_map(fn($y)=>(int)($yr[$y]??0),$labels);
admin_header('Reports','report');?>
<div class="card"><div class="head"><h1>Cemetery Reports</h1><button class="btn" onclick="print()"><i class="fas fa-print"></i> Print Report</button></div>
<div class="stats"><?php foreach([['Total Graves',q('SELECT COUNT(*) FROM graves')->fetchColumn()],['Available Plots',$c('available')],['Reserved Plots',$c('reserved')],['Occupied Plots',$c('occupied')]] as $s):?><div class="stat"><p><?=$s[0]?></p><b><?=$s[1]?></b></div><?php endforeach;?></div>
<h2>Plot Status Distribution</h2><div style="height:420px"><canvas id="pie"></canvas></div><h2 style="margin-top:30px">Graves by Year (Last 10 Years)</h2><div style="height:320px"><canvas id="bar"></canvas></div></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script><script>
new Chart(pie,{type:'pie',data:{labels:['Available','Reserved','Occupied'],datasets:[{data:[<?=$c('available')?>,<?=$c('reserved')?>,<?=$c('occupied')?>],backgroundColor:['#94d9d6','#fde39b','#fe9fb3']}]},options:{maintainAspectRatio:false,plugins:{legend:{position:'right'}}}});
new Chart(bar,{type:'bar',data:{labels:<?=json_encode($labels)?>,datasets:[{label:'Number of Interments',data:<?=json_encode($vals)?>,backgroundColor:'#90caf9'}]},options:{maintainAspectRatio:false,scales:{y:{beginAtZero:true,ticks:{precision:0}}}}});</script>
<?php admin_footer();
