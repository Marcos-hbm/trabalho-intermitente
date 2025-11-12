<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
requireUser();

$event_id = intval($_POST['event_id'] ?? 0);
if ($event_id <= 0) {
    header('Location: /user/dashboard.php');
    exit;
}

// Verifica se evento existe
$check = $mysqli->prepare("SELECT id FROM events WHERE id = ?");
$check->bind_param('i', $event_id);
$check->execute();
if ($check->get_result()->num_rows === 0) {
    header('Location: /user/dashboard.php?msg=Evento+não+encontrado');
    exit;
}

$stmt = $mysqli->prepare("INSERT IGNORE INTO applications (event_id, user_id) VALUES (?, ?)");
$stmt->bind_param('ii', $event_id, $_SESSION['user_id']);
$stmt->execute();

header('Location: /user/dashboard.php?msg=Candidatado+com+sucesso');