<?php
require 'config.php'; session_start();
if (!isset($_SESSION['role']) || $_SESSION['role']!=='passenger') { header('Location: login.php'); exit; }
$uid = $_SESSION['user_id'];
$routes = $pdo->query('SELECT * FROM routes ORDER BY id')->fetchAll();
$bookings = $pdo->prepare('SELECT b.*, r.origin, r.destination FROM bookings b JOIN routes r ON b.route_id=r.id WHERE b.passenger_id=? ORDER BY b.created_at DESC');
$bookings->execute([$uid]); $bookings = $bookings->fetchAll();
?>
<?php include 'includes/header.php'; ?>
<div class="row">
  <div class="col-md-8">
    <div class="card p-3 mb-3">
      <h5>Make a Booking</h5>
      <form method="post" action="booking.php">
        <div class="mb-2"><label class="form-label">Select Route</label>
          <select name="route_id" class="form-select" required>
            <?php foreach($routes as $r): ?>
              <option value="<?=$r['id']?>"><?=htmlspecialchars($r['origin'].' → '.$r['destination'].' (₦'.number_format($r['price'],0).')')?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-2"><label class="form-label">Passengers</label><input type="number" name="seats" class="form-control" value="1" min="1"></div>
        <div class="d-grid"><button class="btn btn-primary">Request Booking</button></div>
      </form>
    </div>
    <div class="card p-3">
      <h5>My Bookings</h5>
      <table class="table">
        <thead><tr><th>#</th><th>Route</th><th>Seats</th><th>Total (₦)</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
          <?php foreach($bookings as $b): ?>
            <tr>
              <td><?=$b['id']?></td>
              <td><?=htmlspecialchars($b['origin'].' → '.$b['destination'])?></td>
              <td><?=$b['seats']?></td>
              <td><?=number_format($b['total'],2)?></td>
              <td><?=$b['status']?></td>
              <td>
                <?php if($b['status']==='approved'): ?>
                  <a class="btn btn-sm btn-outline-secondary" href="receipt.php?id=<?=$b['id']?>" target="_blank">Print</a>
                <?php else: ?>
                  <button class="btn btn-sm btn-outline-secondary" disabled>Print</button>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3">
      <h5>Profile</h5>
      <p><strong><?=htmlspecialchars($_SESSION['name'])?></strong></p>
      <p class="small">Role: Passenger</p>
      <a href="logout.php" class="btn btn-sm btn-danger">Logout</a>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>