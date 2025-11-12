<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
requireCompany();

$cid = $_SESSION['company_id'];

$events = $mysqli->prepare("SELECT e.id, e.nome, e.descricao, e.event_date, e.image_path, e.vacancies, v.nome as local
                            FROM events e
                            LEFT JOIN venues v ON v.id = e.venue_id
                            WHERE e.company_id=? ORDER BY e.created_at DESC");
$events->bind_param('i', $cid);
$events->execute();
$rows = $events->get_result();

require __DIR__.'/../includes/header.php';
?>
<div class="row">
  <div class="col-lg-3 mb-3">
    <div class="list-group">
      <a href="/company/dashboard.php" class="list-group-item list-group-item-action active">Eventos</a>
      <a href="/company/new_event.php" class="list-group-item list-group-item-action">+ Novo evento</a>
      <a href="/company/employees.php" class="list-group-item list-group-item-action">Funcionários</a>
      <a href="/company/venues.php" class="list-group-item list-group-item-action">Credenciamentos</a>
      <a href="/company/profile.php" class="list-group-item list-group-item-action">Perfil</a>
    </div>
  </div>
  <div class="col-lg-9">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2>Meus Eventos</h2>
      <a href="/company/new_event.php" class="btn btn-primary btn-icon"><span class="bi bi-plus"></span>Novo evento</a>
    </div>
    <div class="row g-3">
      <?php while ($e = $rows->fetch_assoc()): ?>
      <div class="col-md-6">
        <div class="card h-100">
          <?php if ($e['image_path']): ?>
            <img src="/<?=htmlspecialchars($e['image_path'])?>" class="card-img-top" alt="Imagem">
          <?php else: ?>
            <img src="https://picsum.photos/seed/e<?=intval($e['id'])?>/600/300" class="card-img-top" alt="">
          <?php endif; ?>
          <div class="card-body d-flex flex-column">
            <h5 class="card-title"><?=htmlspecialchars($e['nome'])?></h5>
            <p class="card-text"><?=nl2br(htmlspecialchars(mb_strimwidth($e['descricao'] ?? '', 0, 140, '...')))?></p>
            <div class="mt-auto">
              <span class="badge bg-info">Data: <?=htmlspecialchars(date('d/m/Y', strtotime($e['event_date'])))?></span>
              <span class="badge bg-secondary">Vagas: <?=intval($e['vacancies'])?></span>
              <span class="badge bg-light text-dark"><?=htmlspecialchars($e['local'] ?? 'Local a definir')?></span>
            </div>
          </div>
          <div class="card-footer bg-white d-flex gap-2">
            <a class="btn btn-outline-primary btn-sm" href="/company/event.php?id=<?=intval($e['id'])?>">Gerenciar</a>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>
<?php require __DIR__.'/../includes/footer.php'; ?>