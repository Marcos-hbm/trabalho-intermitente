<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
requireCompany();

$cid = $_SESSION['company_id'];

$favs = $mysqli->prepare("SELECT u.id, u.nome, u.telefone, u.email FROM favorites f JOIN users u ON u.id=f.user_id WHERE f.company_id=? ORDER BY u.nome ASC");
$favs->bind_param('i', $cid);
$favs->execute();
$favlist = $favs->get_result();

$sel = $mysqli->prepare("SELECT DISTINCT u.id, u.nome, u.telefone, u.email
                         FROM selections s
                         JOIN users u ON u.id=s.user_id
                         JOIN events e ON e.id=s.event_id
                         WHERE e.company_id=?
                         ORDER BY u.nome ASC");
$sel->bind_param('i', $cid);
$sel->execute();
$sellist = $sel->get_result();

require __DIR__.'/../includes/header.php';
?>
<h2>Funcionários</h2>
<div class="row">
  <div class="col-md-6">
    <div class="card mb-3">
      <div class="card-header">Favoritos</div>
      <div class="list-group list-group-flush">
      <?php while ($u = $favlist->fetch_assoc()): ?>
        <div class="list-group-item d-flex justify-content-between align-items-center">
          <div>
            <div class="fw-semibold"><?=htmlspecialchars($u['nome'])?></div>
            <div class="small text-muted"><?=htmlspecialchars($u['email'])?> • <?=htmlspecialchars($u['telefone'])?></div>
          </div>
          <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary btn-sm" href="/company/view_user.php?id=<?=$u['id']?>" target="_blank">Perfil</a>
            <a class="btn btn-warning btn-sm" href="/company/favorite_toggle.php?user_id=<?=$u['id']?>&fav=0&back=<?=urlencode($_SERVER['REQUEST_URI'])?>">Remover</a>
            <a class="btn btn-outline-primary btn-sm" href="/company/message.php?user_id=<?=$u['id']?>">Mensagem</a>
          </div>
        </div>
      <?php endwhile; ?>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card mb-3">
      <div class="card-header">Selecionados (em qualquer evento)</div>
      <div class="list-group list-group-flush">
      <?php while ($u = $sellist->fetch_assoc()): ?>
        <div class="list-group-item d-flex justify-content-between align-items-center">
          <div>
            <div class="fw-semibold"><?=htmlspecialchars($u['nome'])?></div>
            <div class="small text-muted"><?=htmlspecialchars($u['email'])?> • <?=htmlspecialchars($u['telefone'])?></div>
          </div>
          <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary btn-sm" href="/company/view_user.php?id=<?=$u['id']?>" target="_blank">Perfil</a>
            <a class="btn btn-outline-warning btn-sm" href="/company/favorite_toggle.php?user_id=<?=$u['id']?>&fav=1&back=<?=urlencode($_SERVER['REQUEST_URI'])?>">Favoritar</a>
            <a class="btn btn-outline-primary btn-sm" href="/company/message.php?user_id=<?=$u['id']?>">Mensagem</a>
          </div>
        </div>
      <?php endwhile; ?>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__.'/../includes/footer.php'; ?>