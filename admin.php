<?php
require 'config.php'; session_start();
if (!isset($_SESSION['role']) || $_SESSION['role']!=='admin') { header('Location: login.php'); exit; }
// POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['action']) && $_POST['action']==='add_route') {
    $origin = $_POST['origin']; $dest = $_POST['destination']; $price = floatval($_POST['price']);
    $pdo->prepare('INSERT INTO routes (origin,destination,price,created_at) VALUES (?,?,?,NOW())')->execute([$origin,$dest,$price]);
    $_SESSION['flash']='Route added.'; header('Location: admin.php#routes'); exit;
  }
  if (isset($_POST['action']) && $_POST['action']==='edit_route') {
    $id = intval($_POST['route_id']); $origin = $_POST['origin']; $dest = $_POST['destination']; $price = floatval($_POST['price']);
    $pdo->prepare('UPDATE routes SET origin=?,destination=?,price=?,updated_at=NOW() WHERE id=?')->execute([$origin,$dest,$price,$id]);
    $_SESSION['flash']='Route updated.'; header('Location: admin.php#routes'); exit;
  }
  if (isset($_POST['action']) && $_POST['action']==='edit_passenger') {
    $id = intval($_POST['pid']); $name = $_POST['name']; $email = $_POST['email']; $phone = $_POST['phone'];
    $pdo->prepare('UPDATE passengers SET name=?,email=?,phone=?,updated_at=NOW() WHERE id=?')->execute([$name,$email,$phone,$id]);
    $_SESSION['flash']='Passenger updated.'; header('Location: admin.php#passengers'); exit;
  }
}
// GET actions
if (isset($_GET['approve_pass'])) { $pdo->prepare('UPDATE passengers SET status=? WHERE id=?')->execute(['approved', intval($_GET['approve_pass'])]); $_SESSION['flash']='Passenger approved.'; header('Location: admin.php#passengers'); exit; }
if (isset($_GET['reject_pass'])) { $pdo->prepare('UPDATE passengers SET status=? WHERE id=?')->execute(['rejected', intval($_GET['reject_pass'])]); $_SESSION['flash']='Passenger rejected.'; header('Location: admin.php#passengers'); exit; }
if (isset($_GET['del_pass'])) { $pdo->prepare('DELETE FROM passengers WHERE id=?')->execute([intval($_GET['del_pass'])]); $_SESSION['flash']='Passenger deleted.'; header('Location: admin.php#passengers'); exit; }
if (isset($_GET['del_route'])) { $pdo->prepare('DELETE FROM routes WHERE id=?')->execute([intval($_GET['del_route'])]); $_SESSION['flash']='Route deleted.'; header('Location: admin.php#routes'); exit; }
if (isset($_GET['approve_book'])) { $pdo->prepare('UPDATE bookings SET status=? WHERE id=?')->execute(['approved', intval($_GET['approve_book'])]); $_SESSION['flash']='Booking approved.'; header('Location: admin.php#bookings'); exit; }
if (isset($_GET['reject_book'])) { $pdo->prepare('UPDATE bookings SET status=? WHERE id=?')->execute(['rejected', intval($_GET['reject_book'])]); $_SESSION['flash']='Booking rejected.'; header('Location: admin.php#bookings'); exit; }
if (isset($_GET['del_book'])) { $pdo->prepare('DELETE FROM bookings WHERE id=?')->execute([intval($_GET['del_book'])]); $_SESSION['flash']='Booking deleted.'; header('Location: admin.php#bookings'); exit; }

$passengers = $pdo->query('SELECT * FROM passengers ORDER BY created_at DESC')->fetchAll();
$routes = $pdo->query('SELECT * FROM routes ORDER BY id')->fetchAll();
$bookings = $pdo->query('SELECT b.*, p.name as passenger_name, r.origin, r.destination FROM bookings b JOIN passengers p ON b.passenger_id=p.id JOIN routes r ON b.route_id=r.id ORDER BY b.created_at DESC')->fetchAll();
?>
<?php include 'includes/header.php'; ?>
<?php if(!empty($_SESSION['flash'])){ echo '<div class="alert alert-success">'.$_SESSION['flash'].'</div>'; unset($_SESSION['flash']); } ?>
<div class="card p-3">
  <ul class="nav nav-tabs" id="adminTabs" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#passengers">Passengers</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#routes">Routes</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#bookings">Bookings</button></li>
  </ul>
  <div class="tab-content p-3">
    <div class="tab-pane fade show active" id="passengers">
      <h5>Passengers</h5>
      <table class="table table-striped"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Status</th><th>Actions</th></tr></thead><tbody>
      <?php foreach($passengers as $p): ?>
        <tr><td><?=$p['id']?></td><td><?=htmlspecialchars($p['name'])?></td><td><?=htmlspecialchars($p['email'])?></td><td><?=htmlspecialchars($p['phone'])?></td><td><?=$p['status']?></td>
        <td class="table-actions"><?php if($p['status']!=='approved'): ?><a class="btn btn-sm btn-success" href="?approve_pass=<?=$p['id']?>">Approve</a><?php endif; ?>
          <a class="btn btn-sm btn-warning" href="#" data-bs-toggle="modal" data-bs-target="#editPassenger<?=$p['id']?>">Edit</a>
          <a class="btn btn-sm btn-danger" href="?del_pass=<?=$p['id']?>" onclick="return confirm('Delete passenger?')">Delete</a></td></tr>

        <div class="modal fade" id="editPassenger<?=$p['id']?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
          <form method="post">
          <div class="modal-header"><h5 class="modal-title">Edit Passenger</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <input type="hidden" name="action" value="edit_passenger"><input type="hidden" name="pid" value="<?=$p['id']?>">
            <div class="mb-2"><label class="form-label">Name</label><input name="name" class="form-control" value="<?=htmlspecialchars($p['name'])?>"></div>
            <div class="mb-2"><label class="form-label">Email</label><input name="email" class="form-control" value="<?=htmlspecialchars($p['email'])?>"></div>
            <div class="mb-2"><label class="form-label">Phone</label><input name="phone" class="form-control" value="<?=htmlspecialchars($p['phone'])?>"></div>
          </div>
          <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save</button></div>
          </form></div></div></div>

      <?php endforeach; ?></tbody></table>
    </div>
    <div class="tab-pane fade" id="routes">
      <h5>Routes</h5><button class="btn btn-sm btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addRoute">Add Route</button>
      <table class="table table-bordered"><thead><tr><th>ID</th><th>Origin</th><th>Destination</th><th>Price (₦)</th><th>Actions</th></tr></thead><tbody>
      <?php foreach($routes as $r): ?>
        <tr><td><?=$r['id']?></td><td><?=htmlspecialchars($r['origin'])?></td><td><?=htmlspecialchars($r['destination'])?></td><td><?=number_format($r['price'],2)?></td>
        <td><a class="btn btn-sm btn-warning" href="#" data-bs-toggle="modal" data-bs-target="#editRoute<?=$r['id']?>">Edit</a> <a class="btn btn-sm btn-danger" href="?del_route=<?=$r['id']?>" onclick="return confirm('Delete route?')">Delete</a></td></tr>

        <div class="modal fade" id="editRoute<?=$r['id']?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
          <form method="post">
          <div class="modal-header"><h5 class="modal-title">Edit Route</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body"><input type="hidden" name="action" value="edit_route"><input type="hidden" name="route_id" value="<?=$r['id']?>">
            <div class="mb-2"><label class="form-label">Origin</label><input name="origin" class="form-control" value="<?=htmlspecialchars($r['origin'])?>"></div>
            <div class="mb-2"><label class="form-label">Destination</label><input name="destination" class="form-control" value="<?=htmlspecialchars($r['destination'])?>"></div>
            <div class="mb-2"><label class="form-label">Price</label><input name="price" class="form-control" value="<?=htmlspecialchars($r['price'])?>"></div>
          </div>
          <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save</button></div>
          </form></div></div></div>

      <?php endforeach; ?></tbody></table>

      <div class="modal fade" id="addRoute" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
        <form method="post"><div class="modal-header"><h5 class="modal-title">Add Route</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"><input type="hidden" name="action" value="add_route">
          <div class="mb-2"><label class="form-label">Origin</label><input name="origin" class="form-control" required></div>
          <div class="mb-2"><label class="form-label">Destination</label><input name="destination" class="form-control" required></div>
          <div class="mb-2"><label class="form-label">Price (₦)</label><input name="price" class="form-control" required></div>
        </div><div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Add</button></div></form>
      </div></div></div>

    </div>
    <div class="tab-pane fade" id="bookings">
      <h5>Bookings</h5>
      <table class="table table-hover"><thead><tr><th>ID</th><th>Passenger</th><th>Route</th><th>Seats</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead><tbody>
      <?php foreach($bookings as $b): ?>
        <tr><td><?=$b['id']?></td><td><?=htmlspecialchars($b['passenger_name'])?></td><td><?=htmlspecialchars($b['origin'].' → '.$b['destination'])?></td><td><?=$b['seats']?></td><td><?=number_format($b['total'],2)?></td><td><?=$b['status']?></td>
        <td><?php if($b['status']!=='approved'): ?><a class="btn btn-sm btn-success" href="?approve_book=<?=$b['id']?>">Approve</a><?php endif; ?> <?php if($b['status']!=='rejected'): ?><a class="btn btn-sm btn-danger" href="?reject_book=<?=$b['id']?>">Reject</a><?php endif; ?> <a class="btn btn-sm btn-outline-danger" href="?del_book=<?=$b['id']?>" onclick="return confirm('Delete booking?')">Delete</a></td></tr>
      <?php endforeach; ?></tbody></table>
    </div>
  </div>
</div>
<div class="mt-3"><a href="logout.php" class="btn btn-sm btn-danger">Logout</a></div>
<?php include 'includes/footer.php'; ?>