<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
requireCompany();

$cid = $_SESSION['company_id'];
$venues = $mysqli->prepare("SELECT id, nome FROM venues WHERE company_id=? ORDER BY created_at DESC");
$venues->bind_param('i', $cid);
$venues->execute();
$vlist = $venues->get_result();

require __DIR__.'/../includes/header.php';
?>
<h2>Novo Evento</h2>
<form method="post" action="/company/save_event.php" enctype="multipart/form-data" class="card card-body">
  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">Nome</label>
      <input type="text" class="form-control" name="nome" required>
    </div>
    <div class="col-md-3 mb-3">
      <label class="form-label">Data do evento</label>
      <input type="date" class="form-control" name="event_date" required>
    </div>
    <div class="col-md-3 mb-3">
      <label class="form-label">Vagas</label>
      <input type="number" class="form-control" name="vacancies" min="1" value="1" required>
    </div>
    <div class="col-md-12 mb-3">
      <label class="form-label">Descrição</label>
      <textarea class="form-control" name="descricao" rows="4"></textarea>
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Imagem do evento</label>
      <input type="file" class="form-control" name="imagem" accept="image/*">
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Credenciamento (local)</label>
      <select class="form-select" name="venue_id">
        <option value="">Selecionar...</option>
        <?php while ($v = $vlist->fetch_assoc()): ?>
          <option value="<?=$v['id']?>"><?=htmlspecialchars($v['nome'])?></option>
        <?php endwhile; ?>
      </select>
      <div class="form-text">Cadastre novos credenciamentos em "Credenciamentos".</div>
    </div>
  </div>
  <div class="d-flex justify-content-end gap-2">
    <a href="/company/dashboard.php" class="btn btn-outline-secondary">Cancelar</a>
    <button class="btn btn-success">Criar evento</button>
  </div>
</form>
<?php require __DIR__.'/../includes/footer.php'; ?>