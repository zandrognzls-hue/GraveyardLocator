<?php require __DIR__.'/../includes/bootstrap.php';require_admin();
$plots=q('SELECT id,section,row_no,plot_no,status,map_x,map_y,map_polygon FROM plots ORDER BY section,row_no,plot_no')->fetchAll();
admin_header('Cemetery Map','map_coordinate_tool');?>
<div class="tool"><div class="card" style="padding:15px"><div class="mapbox" id="map"></div></div>
<div class="card"><div class="seg"><button type="button" id="m1" class="on"><i class="fas fa-map-pin"></i> Map Existing Plot</button><button type="button" id="m2"><i class="fas fa-plus-circle"></i> Create New Plot</button></div>
<div id="ex"><label>Select Existing Plot:</label><select id="sel"><option value="">-- Select a Plot --</option><?php foreach($plots as $p):?><option value="<?=$p['id']?>"><?=h($p['section'].' / Row '.$p['row_no'].' / '.$p['plot_no'])?></option><?php endforeach;?></select></div>
<div id="nw" hidden><div class="grid2"><div><label>Section</label><input id="ns"></div><div><label>Row</label><input id="nr" type="number"></div></div><label>Plot number</label><input id="np"></div>
<label>Current Coordinates:</label><input id="cur" readonly placeholder="Click on the map to add corner points"><label>Cursor Position:</label><input id="cp" readonly placeholder="Hover over map"><p style="color:#777;margin:10px 0 18px">Click the corners of the plot area (3 or more), then save. Drag to pan, scroll to zoom.</p>
<button class="btn" id="save"><i class="fas fa-save"></i> Save Plot Area</button><button class="btn bl" id="cc"><i class="fas fa-eraser"></i> Clear Current</button><button class="btn or" id="ca"><i class="fas fa-trash"></i> Clear Canvas</button><button class="btn pu" id="ld"><i class="fas fa-download"></i> Load Saved Coords</button>
<h3 style="font-weight:500">Mapped Plots (<span id="mc">0</span>):</h3><div class="mlist" id="ml"></div></div></div>
<script src="<?=url('assets/js/map.js')?>"></script><script>
const T=<?=json_encode(csrf())?>,plots=<?=json_encode($plots,JSON_HEX_TAG)?>,$=i=>document.getElementById(i);let mode=1,pts=[];
const map=new CemMap($('map'),{img:<?=json_encode(url('assets/img/cemetery-map.png'))?>,plots,
  onPoint:p=>{pts.push([p.x,p.y]);map.draft=pts;map.render();$('cur').value=pts.length+' points: '+pts.map(q=>q.join(',')).join(' ')},
  onMove:p=>{$('cp').value=p?`x=${p.x}%, y=${p.y}%`:''}});
function list(){const m=plots.filter(p=>p.map_polygon);$('mc').textContent=m.length;$('ml').innerHTML=m.map(p=>`<div><span><b style="color:#4caf50">${p.plot_no}</b>: x=${p.map_x}%, y=${p.map_y}%</span><button class="btn sm rd" data-id="${p.id}" aria-label="Remove mapping">&times;</button></div>`).join('')||'<p style="color:#999;text-align:center">No plots mapped yet.</p>'}
list();
$('ml').onclick=async e=>{const b=e.target.closest('button');if(!b||!confirm('Remove this plot from the map?'))return;await post({action:'unmap',plot_id:b.dataset.id});const p=plots.find(x=>x.id==b.dataset.id);p.map_polygon=p.map_x=p.map_y=null;map.render();list()};
$('m1').onclick=()=>sw(1);$('m2').onclick=()=>sw(2);function sw(m){mode=m;$('m1').classList.toggle('on',m==1);$('m2').classList.toggle('on',m==2);$('ex').hidden=m==2;$('nw').hidden=m==1}
$('sel').onchange=()=>{const id=$('sel').value;if(id)map.focus(id)};
$('cc').onclick=()=>{pts=[];map.draft=[];map.render();$('cur').value=''};
$('ca').onclick=()=>{$('cc').click();map.hide=true;map.render()};
$('ld').onclick=()=>{map.hide=false;map.render()};
async function post(d){const f=new FormData();f.append('csrf',T);for(const k in d)f.append(k,d[k]);return (await fetch('api_map.php',{method:'POST',body:f})).json()}
$('save').onclick=async()=>{const d={action:'save',polygon:JSON.stringify(pts)};
  if(mode==1){if(!$('sel').value)return alert('Select a plot first.');d.plot_id=$('sel').value}else{d.section=$('ns').value;d.row_no=$('nr').value;d.plot_no=$('np').value}
  const r=await post(d);if(!r.ok)return alert(r.error);alert('Plot area saved.');location.reload()};
</script><?php admin_footer();
