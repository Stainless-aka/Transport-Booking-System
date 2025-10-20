<?php
require 'config.php'; session_start();
$msg='';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? ''); $email = trim($_POST['email'] ?? ''); $phone = trim($_POST['phone'] ?? ''); $password = $_POST['password'] ?? '';
  if (!$name || !$email || !$password) $msg='Please fill required fields.';
  else {
    $st = $pdo->prepare('SELECT id FROM passengers WHERE email=? LIMIT 1'); $st->execute([$email]);
    if ($st->fetch()) $msg='Email already registered.';
    else { $ins = $pdo->prepare('INSERT INTO passengers (name,email,phone,password,status,created_at) VALUES (?,?,?,?,?,NOW())'); $ins->execute([$name,$email,$phone,$password,'pending']); $msg='Registration successful. Wait for admin approval.'; }
  }
}
?>
<?php include 'includes/header.php'; ?>
<div class="row justify-content-center"><div class="col-md-7"><div class="card p-4">
  <h4>Passenger Registration</h4>
  <?php if($msg): ?><div class="alert alert-info"><?=$msg?></div><?php endif; ?>
  <form method="post">
    <div class="mb-2"><label class="form-label">Full name</label><input class="form-control" name="name" required></div>
    <div class="mb-2"><label class="form-label">Email</label><input class="form-control" type="email" name="email" required></div>
    <div class="mb-2"><label class="form-label">Phone</label><input class="form-control" name="phone"></div>
    <div class="mb-2"><label class="form-label">Password</label><input class="form-control" type="password" name="password" required></div>
    <div class="d-grid"><button class="btn btn-success">Register</button></div>
  </form>
</div></div></div>
<?php include 'includes/footer.php'; ?>