<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 check_csrf();
 try {
  $pdo=db();
  $pdo->exec(file_get_contents(__DIR__.'/schema.sql'));
  $count=(int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
  if($count!==0)throw new RuntimeException('Already installed. Sign in instead.');
  $name=trim((string)($_POST['name']??''));$email=strtolower(trim((string)($_POST['email']??'')));$password=(string)($_POST['password']??'');
  if(mb_strlen($name)<2||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($password)<12)throw new RuntimeException('Enter a name, valid email and password of at least 12 characters.');
  $q=$pdo->prepare("INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?,'admin')");
  $q->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT)]);
  flash('Admin account created. Sign in to your POS.');redirect('login');
 } catch(Throwable $ex){$error=$ex->getMessage();}
}
?><!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Install POS</title><link rel="stylesheet" href="assets/style.css"><body class="auth"><main class="auth-card"><h1>Set up your POS</h1><p>Configure the MySQL database first. Create your first admin account.</p><?php if(isset($error)):?><div class="alert error"><?=e($error)?></div><?php endif;?><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><label>Your name<input required name="name" minlength="2"></label><label>Email<input required name="email" type="email"></label><label>Admin password (12+ characters)<input required name="password" type="password" minlength="12"></label><button class="primary" type="submit">Create admin</button></form><p class="muted">After installation, remove or restrict access to install.php on your server.</p></main></body></html>
