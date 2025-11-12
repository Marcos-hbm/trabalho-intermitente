<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
requireCompany();

$cid = $_SESSION['company_id'];
$event_id = intval($_GET['id'] ?? 0);

$event = $mysqli->prepare("SELECT e.*, v.nome as local FROM events e LEFT JOIN venues v ON v.id=e.venue_id WHERE e.id=? AND e.company_id=?");
$event->bind_param('ii', $event_id, $cid);
$event->execute();
$ev = $event->get_result()->fetch_assoc();
if (!$ev) {
    header('Location: /company/dashboard.php?err=Evento+não+encontrado');
    exit;
}

$busca = trim($_GET['q'] ?? '');

// Lista candidatos com favoritos primeiro
$sql = "SELECT u.id, u.nome, u.cpf, u.telefone, u.email,
        CASE WHEN f.user_id IS NOT NULL THEN 1 ELSE 0 END AS favorito,
        CASE WHEN s.user_id IS NOT NULL THEN 1 ELSE 0 END AS selecionado
        FROM applications a
        JOIN users u ON u.id = a.user_id
        LEFT JOIN favorites f ON f.company_id=? AND f.user_id=u.id
        LEFT JOIN selections s ON s.event_id=? AND s.user_id=u.id
        WHERE a.event_id=?";
$params = [$cid, $event_id, $event_id];
$types = 'iii';
if ($busca !== '') {
    $sql .= " AND u.nome LIKE CONCAT('%', ?, '%')";
    $params[] = $busca;
    $types .= 's';
}
$sql .= " ORDER BY favorito DESC, u.nome ASC";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$cands = $stmt->get_result();

// Contagem de selecionados
$count = $mysqli->prepare("SELECT COUNT(*) FROM selections WHERE event_id=?");
$count->bind_param('i', $event_id);
$count->execute();
$count->bind_result($sel_count);
$count->fetch();
$count->close();

require __DIR__.'/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h2><?=htmlspecialchars($ev['nome'])?></h2>
    <div class="text-muted">
      Data: <?=htmlspecialchars(date('d/m/Y', strtotime($ev['event_date'])))?> •
      Vagas: <?=intval($ev['vacancies'])?> •
      Selecionados: <strong><?=$sel_count?></strong> •
      Local: <?=htmlspecialchars($ev['local'] ?? 'A definir')?>
    </div>
  </div>
  <div class="d-flex gap-2">
    <a href="/company/export_excel.php?event_id=<?=$event_id?>" class="btn btn-success btn-sm">Exportar Excel</a>
    <a href="/company/dashboard.php" class="btn btn-outline-secondary btn-sm">Voltar</a>
  </div>
</div>

<form class="row g-2 mb-3" method="get">
  <input type="hidden" name="id" value="<?=$event_id?>">
  <div class="col-auto">
    <input class="form-control" name="q" value="<?=htmlspecialchars($busca)?>" placeholder="Pesquisar por nome">
  </div>
  <div class="col-auto">
    <button class="btn btn-outline-primary">Buscar</button>
  </div>
</form>

<form method="post" action="/company/select_candidates.php" id="form-cands">
  <input type="hidden" name="event_id" value="<?=$event_id?>">
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th><input type="checkbox" onclick="document.querySelectorAll('.chk').forEach(c=>c.checked=this.checked)"></th>
          <th>Nome</th>
          <th>CPF</th>
          <th>Telefone</th>
          <th>E-mail</th>
          <th>Favorito</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($u = $cands->fetch_assoc()): ?>
        <tr class="<?= $u['selecionado'] ? 'table-success' : '' ?>">
          <td><input type="checkbox" class="form-check-input chk" name="user_ids[]" value="<?=$u['id']?>"></td>
          <td><a href="/company/view_user.php?id=<?=$u['id']?>" target="_blank"><?=htmlspecialchars($u['nome'])?></a></td>
          <td><?=htmlspecialchars($u['cpf'])?></td>
          <td><?=htmlspecialchars($u['telefone'])?></td>
          <td><?=htmlspecialchars($u['email'])?></td>
          <td>
            <?php if ($u['favorito']): ?>
              <a class="btn btn-warning btn-sm" href="/company/favorite_toggle.php?user_id=<?=$u['id']?>&fav=0&back=<?=urlencode($_SERVER['REQUEST_URI'])?>">Desfavoritar</a>
            <?php else: ?>
              <a class="btn btn-outline-warning btn-sm" href="/company/favorite_toggle.php?user_id=<?=$u['id']?>&fav=1&back=<?=urlencode($_SERVER['REQUEST_URI'])?>">Favoritar</a>
            <?php endif; ?>
          </td>
          <td class="d-flex gap-2">
            <a class="btn btn-outline-secondary btn-sm" href="/company/view_user.php?id=<?=$u['id']?>" target="_blank">Perfil</a>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <div class="d-flex gap-2">
    <button name="action" value="select_marked" class="btn btn-primary">Selecionar marcados</button>
    <button name="action" value="select_until_limit" class="btn btn-outline-primary">Selecionar primeiros até o limite</button>
    <a href="/company/remove_all_selection.php?event_id=<?=$event_id?>" class="btn btn-outline-danger" data-confirm="Remover todos os selecionados deste evento?">Remover tudo</a>
  </div>
</form>
<?php require __DIR__.'/../includes/footer.php'; ?>