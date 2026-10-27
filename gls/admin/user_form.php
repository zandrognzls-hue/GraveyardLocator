<?php
require __DIR__.'/../includes/bootstrap.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$u = $id ? q('SELECT * FROM users WHERE id=?', [$id])->fetch() : null;
if ($id && !$u) {
    go('admin/users.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pw = (string)($_POST['password'] ?? '');
    $min = (int)setting('min_password', 8);

    $username = trim((string)($_POST['username'] ?? ''));
    $fullName = trim((string)($_POST['full_name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $role = (($_POST['role'] ?? '') === 'admin') ? 'admin' : 'user';

    if (($pw !== '' || !$u) && strlen($pw) < $min) {
        flash("Password must be at least $min characters.", 'err');
    } else {
        try {
            if ($u) {
                q('UPDATE users SET username=?,full_name=?,email=?,role=? WHERE id=?', [$username, $fullName, $email, $role, $id]);
                if ($pw !== '') {
                    q('UPDATE users SET password=? WHERE id=?', [password_hash($pw, PASSWORD_DEFAULT), $id]);
                }
            } else {
                q('INSERT INTO users(username,full_name,email,role,password) VALUES(?,?,?,?,?)', [$username, $fullName, $email, $role, password_hash($pw, PASSWORD_DEFAULT)]);
            }
            flash('User saved.');
            go('admin/users.php');
        } catch (PDOException $e) {
            flash('Username or email is already in use.', 'err');
        }
    }
}

$f = $u ?? ['username' => '', 'full_name' => '', 'email' => '', 'role' => 'user'];
admin_header($u ? 'Edit User' : 'Add New User', 'users');
?>
<div class="card"><div class="head"><h1><?=$u ? 'Edit User' : 'Add New User'?></h1></div><form method="post"><?=csrf_field()?><div class="grid2">
<div><label>Username</label><input name="username" value="<?=h($f['username'])?>" required></div><div><label>Full name</label><input name="full_name" value="<?=h($f['full_name'])?>" required></div>
<div><label>Email</label><input type="email" name="email" value="<?=h($f['email'])?>" required></div><div><label>Role</label><select name="role"><option value="user" <?=$f['role']==='user'?'selected':''?>>User</option><option value="admin" <?=$f['role']==='admin'?'selected':''?>>Admin</option></select></div>
<div><label>Password <?=$u ? '(leave blank to keep)' : ''?></label><input type="password" name="password" <?=$u ? '' : 'required'?>></div></div>
<p><button class="btn"><i class="fas fa-save"></i> Save user</button> <a class="btn gr" href="users.php">Cancel</a></p></form></div>
<?php admin_footer();
