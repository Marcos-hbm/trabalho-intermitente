<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
requireCompany();

$cid = $_SESSION['company_id'];
$event_id = intval($_GET['event_id'] ?? 0);

$ev = $mysqli->prepare("SELECT id, nome FROM events WHERE id=? AND company_id=?");
$ev->bind_param('ii', $event_id, $cid);
$ev->execute();
$event = $ev->get_result()->fetch_assoc();
if (!$event) {
    die('Evento inválido');
}

$sel = $mysqli->prepare("SELECT u.nome, u.cpf, u.telefone, u.email
                         FROM selections s JOIN users u ON u.id=s.user_id
                         WHERE s.event_id=?
                         ORDER BY u.nome ASC");
$sel->bind_param('i', $event_id);
$sel->execute();
$rs = $sel->get_result();

$filename = "selecionados_evento_".$event_id.".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="'.$filename.'"');
$output = fopen('php://output', 'w');
fputcsv($output, ['Evento', $event['nome']]);
fputcsv($output, ['Nome','CPF','Telefone','Email']);

while ($row = $rs->fetch_assoc()) {
    fputcsv($output, [$row['nome'], $row['cpf'], $row['telefone'], $row['email']]);
}
fclose($output);