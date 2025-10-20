<?php
require 'config.php'; session_start();
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $role = $_POST['role'] ?? 'passenger';
  if ($role === 'admin') {
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE email = ? LIMIT 1');
    $stmt->execute([$email]); $user = $stmt->fetch();
    if ($user && $user['password'] === $password) {
      $_SESSION['user_id']=$user['id']; $_SESSION['role']='admin'; $_SESSION['name']=$user['name'];
      header('Location: admin.php'); exit;
    } else $msg='Invalid admin credentials.';
  } else {
    $stmt = $pdo->prepare('SELECT * FROM passengers WHERE email = ? LIMIT 1');
    $stmt->execute([$email]); $user = $stmt->fetch();
    if ($user) {
      if ($user['status'] !== 'approved') $msg='Account pending admin approval.';
      elseif ($user['password'] === $password) { $_SESSION['user_id']=$user['id']; $_SESSION['role']='passenger'; $_SESSION['name']=$user['name']; header('Location: passenger.php'); exit; }
      else $msg='Invalid credentials.';
    } else $msg='Invalid credentials.';
  }
}
?>
<?php include 'includes/header.php'; ?>
<div class="row justify-content-center"><div class="col-md-6"><div class="card p-4">
  <h4>Login</h4>
  <?php if($msg): ?><div class="alert alert-danger"><?=$msg?></div><?php endif; ?>
  <form method="post">
    <div class="mb-2"><label class="form-label">Email</label><input class="form-control" name="email" type="email" required></div>
    <div class="mb-2"><label class="form-label">Password</label><input class="form-control" name="password" type="password" required></div>
    <div class="mb-2"><label class="form-label">Login as</label><select name="role" class="form-select"><option value="passenger">Passenger</option><option value="admin">Admin</option></select></div>
    <div class="d-grid"><button class="btn btn-primary">Login</button></div>
  </form>
  <p class="mt-3 small text-muted">Admin: admin@example.com / admin<br>Passengers: james@example.com / 1234, timothy@example.com / 1234</p>
</div></div></div>
<?php include 'includes/footer.php'; ?>