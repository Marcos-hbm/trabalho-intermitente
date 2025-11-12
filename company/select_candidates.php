<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
requireCompany();

$cid = $_SESSION['company_id'];
$event_id = intval($_POST['event_id'] ?? 0);
$action = $_POST['action'] ?? '';

$ev = $mysqli->prepare("SELECT id, company_id, vacancies FROM events WHERE id=? AND company_id=?");
$ev->bind_param('ii', $event_id, $cid);
$ev->execute();
$event = $ev->get_result()->fetch_assoc();
if (!$event) {
    header('Location: /company/dashboard.php?err=Evento+inválido');
    exit;
}

if ($action === 'select_marked') {
    $user_ids = $_POST['user_ids'] ?? [];
    if (!is_array($user_ids)) $user_ids = [];
    // Conta já selecionados
    $cnt = $mysqli->prepare("SELECT COUNT(*) FROM selections WHERE event_id=?");
    $cnt->bind_param('i', $event_id);
    $cnt->execute();
    $cnt->bind_result($sel_count);
    $cnt->fetch();
    $cnt->close();

    $limit_left = max(0, intval($event['vacancies']) - intval($sel_count));
    if ($limit_left > 0 && !empty($user_ids)) {
        // Seleciona até sobrar vagas
        $to_select = array_slice(array_map('intval', $user_ids), 0, $limit_left);
        $ins = $mysqli->prepare("INSERT IGNORE INTO selections (event_id, user_id) VALUES (?, ?)");
        foreach ($to_select as $uid) {
            $ins->bind_param('ii', $event_id, $uid);
            $ins->execute();
        }
    }
} elseif ($action === 'select_until_limit') {
    // Seleciona primeiros (ordenados por favorito desc, nome) até o limite
    $sql = "SELECT u.id
            FROM applications a
            JOIN users u ON u.id = a.user_id
            LEFT JOIN favorites f ON f.company_id=? AND f.user_id=u.id
            WHERE a.event_id=?
            ORDER BY (f.user_id IS NOT NULL) DESC, u.nome ASC";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ii', $cid, $event_id);
    $stmt->execute();
    $rs = $stmt->get_result();

    // Contar selecionados atuais
    $cnt = $mysqli->prepare("SELECT COUNT(*) FROM selections WHERE event_id=?");
    $cnt->bind_param('i', $event_id);
    $cnt->execute();
    $cnt->bind_result($sel_count);
    $cnt->fetch();
    $cnt->close();

    $limit_left = max(0, intval($event['vacancies']) - intval($sel_count));
    if ($limit_left > 0) {
        $ins = $mysqli->prepare("INSERT IGNORE INTO selections (event_id, user_id) VALUES (?, ?)");
        while ($row = $rs->fetch_assoc()) {
            if ($limit_left <= 0) break;
            $uid = intval($row['id']);
            $ins->bind_param('ii', $event_id, $uid);
            $ins->execute();
            if ($ins->affected_rows >= 0) {
                $limit_left--;
            }
        }
    }
}

header('Location: /company/event.php?id='.$event_id);