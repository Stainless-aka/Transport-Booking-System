<?php
require 'config.php'; session_start();
if (!isset($_SESSION['role']) || $_SESSION['role']!=='passenger') { header('Location: login.php'); exit; }
$id = intval($_GET['id'] ?? 0);
$st = $pdo->prepare('SELECT b.*, p.name as passenger_name, r.origin, r.destination FROM bookings b JOIN passengers p ON b.passenger_id=p.id JOIN routes r ON b.route_id=r.id WHERE b.id=? AND b.passenger_id=? LIMIT 1');
$st->execute([$id, $_SESSION['user_id']]); $b = $st->fetch();
if (!$b || $b['status'] !== 'approved') { echo 'Receipt not available.'; exit; }
?>
<!doctype html><html><head><meta charset="utf-8"><title>Receipt</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{font-family:Arial;padding:24px} .receipt{max-width:720px;margin:auto;border:1px solid #ddd;padding:18px;border-radius:8px}</style>
</head><body onload="window.print()">
<div class="receipt">
  <h3>Online Transport Booking System</h3>
  <p><strong>Booking ID:</strong> <?=$b['id']?> | <strong>Date:</strong> <?=$b['created_at']?></p>
  <hr>
  <p><strong>Passenger:</strong> <?=htmlspecialchars($b['passenger_name'])?></p>
  <p><strong>Route:</strong> <?=htmlspecialchars($b['origin'].' → '.$b['destination'])?></p>
  <p><strong>Seats:</strong> <?=$b['seats']?> | <strong>Total (₦):</strong> <?=number_format($b['total'],2)?></p>
  <p><strong>Status:</strong> <?=$b['status']?></p>
</div>
</body></html>