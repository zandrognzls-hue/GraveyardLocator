<?php require __DIR__.'/../includes/bootstrap.php';require_admin();
if(isset($_POST['delete'])){if((int)$_POST['delete']===(int)$_SESSION['uid'])flash('You cannot delete your own account.','err');else{q('DELETE FROM users WHERE id=?',[$_POST['delete']]);flash('User deleted.');}go('admin/users.php');}
admin_header('Users','users');?>
<div class="card"><div class="head"><h1>User Management</h1><a class="btn" href="user_form.php"><i class="fas fa-plus"></i> Add New User</a></div>
<table><tr><th>Username</th><th>Full Name</th><th>Email</th><th>Role</th><th>Actions</th></tr>
<?php foreach(q('SELECT * FROM users ORDER BY id') as $u):?><tr><td><?=h($u['username'])?></td><td><?=h($u['full_name'])?></td><td><?=h($u['email'])?></td><td><span class="pill <?=$u['role']?>"><?=ucfirst($u['role'])?></span></td>
<td style="white-space:nowrap"><a class="btn sm or" href="user_form.php?id=<?=$u['id']?>"><i class="fas fa-edit"></i> Edit</a> <form method="post" style="display:inline" onsubmit="return confirm('Delete this user?')"><?=csrf_field()?><button class="btn sm rd" name="delete" value="<?=$u['id']?>"><i class="fas fa-trash"></i> Delete</button></form></td></tr><?php endforeach;?></table></div>
<?php admin_footer();
