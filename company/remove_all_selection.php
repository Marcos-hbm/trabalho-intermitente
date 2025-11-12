<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
requireCompany();

$cid = $_SESSION['company_id'];
$event_id = intval($_GET['event_id'] ?? 0);

$ev = $mysqli->prepare("SELECT id FROM events WHERE id=? AND company_id=?");
$ev->bind_param('ii', $event_id, $cid);
$ev->execute();
if ($ev->get_result()->num_rows === 0) {
    header('Location: /company/dashboard.php?err=Evento+inválido');
    exit;
}

$del = $mysqli->prepare("DELETE FROM selections WHERE event_id=?");
$del->bind_param('i', $event_id);
$del->execute();

header('Location: /company/event.php?id='.$event_id);