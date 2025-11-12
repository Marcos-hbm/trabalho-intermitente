<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
requireCompany();

$cid = $_SESSION['company_id'];
$user_id = intval($_GET['user_id'] ?? 0);
$fav = intval($_GET['fav'] ?? 0) === 1;
$back = $_GET['back'] ?? '/company/employees.php';

if ($fav) {
    $stmt = $mysqli->prepare("INSERT IGNORE INTO favorites (company_id, user_id) VALUES (?,?)");
    $stmt->bind_param('ii', $cid, $user_id);
    $stmt->execute();
} else {
    $stmt = $mysqli->prepare("DELETE FROM favorites WHERE company_id=? AND user_id=?");
    $stmt->bind_param('ii', $cid, $user_id);
    $stmt->execute();
}

header('Location: '.$back);