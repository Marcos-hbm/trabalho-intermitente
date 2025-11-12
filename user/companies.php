<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
requireUser();

$res = $mysqli->query("SELECT id, nome, email, telefone FROM companies WHERE accepting_affiliation=1 ORDER BY created_at DESC");
require __DIR__.'/../includes/header.php';
?>
<h2>Empresas com Filiação</h2>
<div class="row g-3">
<?php while ($c = $res->fetch_assoc()): ?>
  <div class="col-md-6 col-lg-4">
    <div class="card h-100">
      <div class="card-body">
        <h5 class="card-title"><?=htmlspecialchars($c['nome'])?></h5>
        <p class="card-text small text-muted">Contato: <?=htmlspecialchars($c['email'])?> • <?=htmlspecialchars($c['telefone'])?></p>
      </div>
    </div>
  </div>
<?php endwhile; ?>
</div>
<?php require __DIR__.'/../includes/footer.php'; ?>