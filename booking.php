<?php
require 'config.php'; session_start();
if (!isset($_SESSION['role']) || $_SESSION['role']!=='passenger') { header('Location: login.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: passenger.php'); exit; }
$pid = $_SESSION['user_id']; $route_id = intval($_POST['route_id'] ?? 0); $seats = max(1, intval($_POST['seats'] ?? 1));
$st = $pdo->prepare('SELECT price FROM routes WHERE id=? LIMIT 1'); $st->execute([$route_id]); $r = $st->fetch();
if (!$r) { $_SESSION['flash']='Invalid route selected.'; header('Location: passenger.php'); exit; }
$total = $r['price'] * $seats;
$ins = $pdo->prepare('INSERT INTO bookings (passenger_id, route_id, seats, total, status, created_at) VALUES (?,?,?,?,?,NOW())');
$ins->execute([$pid,$route_id,$seats,$total,'pending']);
$_SESSION['flash']='Booking request submitted. Await admin approval.'; header('Location: passenger.php'); exit;
?>