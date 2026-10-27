<?php
// Run in the browser to create any missing starter accounts. DELETE this file afterwards.
require __DIR__.'/includes/bootstrap.php';
$admin=q('SELECT id FROM users WHERE username=?',['admin'])->fetch();
if($admin){
  if(q('SELECT id FROM users WHERE username=? AND role=?',['admin','admin'])->fetch()){
    exit('An admin account already exists. Delete setup.php.');
  }
  exit('The username "admin" is already used by a non-admin account. Delete setup.php and choose another username or promote that account in phpMyAdmin.');
}
q('INSERT INTO users(username,full_name,email,password,role) VALUES(?,?,?,?,?)',['admin','Administrator','admin@example.com',password_hash('admin123',PASSWORD_DEFAULT),'admin']);
if(!q('SELECT id FROM users WHERE username=?',['visitor'])->fetch()){
  q('INSERT INTO users(username,full_name,email,password,role) VALUES(?,?,?,?,?)',['visitor','Sample Visitor','visitor@example.com',password_hash('user123',PASSWORD_DEFAULT),'user']);
}
echo 'Admin added: admin / admin123. Delete setup.php now and change the admin password.';
