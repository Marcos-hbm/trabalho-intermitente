<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
requireUser();

$uid = $_SESSION['user_id'];
// Remover do cpf_registry também
$stmt = $mysqli->prepare("DELETE FROM cpf_registry WHERE owner_type='user' AND owner_id=?");
$stmt->bind_param('i', $uid);
$stmt->execute();

// Remoção em cascata cuidará de applications, favorites, selections, messages via FKs
$stmt = $mysqli->prepare("DELETE FROM users WHERE id=?");
$stmt->bind_param('i', $uid);
$stmt->execute();

logoutAll();
header('Location: /?msg=Conta+excluida');