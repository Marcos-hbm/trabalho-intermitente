<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
requireUser();

$busca = trim($_GET['q'] ?? '');
$sql = "SELECT e.id, e.nome, e.descricao, e.event_date, e.image_path, c.nome AS empresa, v.nome AS local
        FROM events e
        JOIN companies c ON c.id = e.company_id
        LEFT JOIN venues v ON v.id = e.venue_id
        WHERE e.event_date >= CURDATE()";
$params = [];
$types = '';
if ($busca !== '') {
    $sql .= " AND (e.nome LIKE CONCAT('%', ?, '%') OR e.descricao LIKE CONCAT('%', ?, '%') OR c.nome LIKE CONCAT('%', ?, '%'))";
    $types .= 'sss';
    $params = [$busca, $busca, $busca];
}
$sql .= " ORDER BY e.event_date ASC, e.id DESC";
$stmt = $mysqli->prepare($sql);
if ($types) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
require __DIR__.'/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Jobs Disponíveis</h2>
  <form class="d-flex" method="get">
    <input class="form-control me-2" name="q" value="<?=htmlspecialchars($busca)?>" placeholder="Pesquisar jobs...">
    <button class="btn btn-outline-primary">Buscar</button>
  </form>
</div>
<div class="row g-3">
<?php while ($row = $result->fetch_assoc()): ?>
  <div class="col-md-6 col-lg-4">
    <div class="card h-100">
      <?php if ($row['image_path']): ?>
        <img src="<?=htmlspecialchars($row['image_path'])?>" class="card-img-top" alt="Evento">
      <?php else: ?>
        <img src="https://picsum.photos/seed/<?=intval($row['id'])?>/600/400" class="card-img-top" alt="">
      <?php endif; ?>
      <div class="card-body d-flex flex-column">
        <h5 class="card-title"><?=htmlspecialchars($row['nome'])?></h5>
        <p class="card-text small text-muted mb-1"><?=htmlspecialchars($row['empresa'])?> • <?=htmlspecialchars($row['local'] ?? 'Local a definir')?></p>
        <p class="card-text flex-grow-1"><?=nl2br(htmlspecialchars(mb_strimwidth($row['descricao'] ?? '', 0, 140, '...')))?></p>
        <div class="mt-2">
          <span class="badge bg-info">Data: <?=htmlspecialchars(date('d/m/Y', strtotime($row['event_date'])))?></span>
        </div>
      </div>
      <div class="card-footer bg-white">
        <form method="post" action="/user/apply.php" class="d-flex justify-content-between">
          <input type="hidden" name="event_id" value="<?=intval($row['id'])?>">
          <button class="btn btn-primary btn-sm">Candidatar-se</button>
          <a class="btn btn-outline-secondary btn-sm" href="/company/view_user.php?preview=1&event_id=<?=intval($row['id'])?>">Detalhes</a>
        </form>
      </div>
    </div>
  </div>
<?php endwhile; ?>
</div>
<?php require __DIR__.'/../includes/footer.php'; ?>