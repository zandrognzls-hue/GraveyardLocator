<?php require __DIR__.'/../includes/bootstrap.php';require_admin();header('Content-Type: application/json');
$a=$_POST['action']??'';
function out($d){echo json_encode($d);exit;}
if($a==='save'){
  $pts=json_decode($_POST['polygon']??'[]',true);
  if(!is_array($pts)||count($pts)<3)out(['ok'=>false,'error'=>'Click at least 3 points on the map.']);
  foreach($pts as &$p){$p=[max(0,min(100,round((float)$p[0],2))),max(0,min(100,round((float)$p[1],2)))];}unset($p);
  $pid=(int)($_POST['plot_id']??0);
  if(!$pid){try{q('INSERT INTO plots(section,row_no,plot_no) VALUES(?,?,?)',[trim($_POST['section']??''),(int)($_POST['row_no']??0),trim($_POST['plot_no']??'')]);$pid=(int)db()->lastInsertId();}
    catch(PDOException $e){out(['ok'=>false,'error'=>'That plot already exists, or the details are incomplete.']);}}
  $cx=array_sum(array_column($pts,0))/count($pts);$cy=array_sum(array_column($pts,1))/count($pts);
  q('UPDATE plots SET map_polygon=?,map_x=?,map_y=? WHERE id=?',[json_encode($pts),round($cx,2),round($cy,2),$pid]);log_activity('Plot area mapped');out(['ok'=>true]);}
if($a==='unmap'){q('UPDATE plots SET map_polygon=NULL,map_x=NULL,map_y=NULL WHERE id=?',[(int)$_POST['plot_id']]);out(['ok'=>true]);}
out(['ok'=>false,'error'=>'Unknown action']);
