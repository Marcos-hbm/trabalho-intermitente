<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
requireCompany();

$cid = $_SESSION['company_id'];

$nome = trim($_POST['nome'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$event_date = $_POST['event_date'] ?? '';
$vacancies = max(1, intval($_POST['vacancies'] ?? 1));
$venue_id = !empty($_POST['venue_id']) ? intval($_POST['venue_id']) : null;

// upload
$image_path = null;
if (!empty($_FILES['imagem']['name'])) {
    @mkdir(__DIR__.'/../uploads/events', 0775, true);
    $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
    $fname = 'uploads/events/c'.$cid.'_'.time().'.'.strtolower($ext);
    if (move_uploaded_file($_FILES['imagem']['tmp_name'], __DIR__.'/../'.$fname)) {
        $image_path = $fname;
    }
}

$stmt = $mysqli->prepare("INSERT INTO events (company_id, nome, descricao, event_date, vacancies, image_path, venue_id) VALUES (?,?,?,?,?,?,?)");
$stmt->bind_param('isssisi', $cid, $nome, $descricao, $event_date, $vacancies, $image_path, $venue_id);
$stmt->execute();

header('Location: /company/dashboard.php?msg=Evento+criado');