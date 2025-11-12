<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
requireCompany();
$cid = $_SESSION['company_id'];

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $nome = trim($_POST['nome'] ?? '');
    $end = trim($_POST['endereco'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $uf = strtoupper(trim($_POST['uf'] ?? ''));
    if ($nome !== '') {
        $ins = $mysqli->prepare("INSERT INTO venues (company_id, nome, endereco, cidade, uf) VALUES (?,?,?,?,?)");
        $ins->bind_param('issss', $cid, $nome, $end, $cidade, $uf);
        $ins->execute();
        $ok = true;
    }
}

if (!empty($_GET['del'])) {
    $vid = intval($_GET['del']);
    $del = $mysqli->prepare("DELETE FROM venues WHERE id=? AND company_id=?");
    $del->bind_param('ii', $vid, $cid);
    $del->execute();
}

$venues = $mysqli->prepare("SELECT id, nome, endereco, cidade, uf FROM venues WHERE company_id=? ORDER BY created_at DESC");
$venues->bind_param('i', $cid);
$venues->execute();
$rs = $venues->get_result();

require __DIR__.'/../includes/header.php';
?>
<h2>Credenciamentos</h2>
<?php if (!empty($ok)): ?><div class="alert alert-success">Credenciamento cadastrado.</div><?php endif; ?>
<div class="row">
  <div class="col-md-5">
    <form method="post" class="card card-body">
      <h5 class="mb-3">Novo credenciamento</h5>
      <div class="mb-3">
        <label class="form-label">Nome</label>
        <input class="form-control" name="nome" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Endereço</label>
        <input class="form-control" name="endereco">
      </div>
      <div class="mb-3">
        <label class="form-label">Cidade</label>
        <input class="form-control" name="cidade">
      </div>
      <div class="mb-3">
        <label class="form-label">UF</label>
        <input class="form-control" name="uf" maxlength="2">
      </div>
      <div class="d-flex justify-content-end">
        <button class="btn btn-success">Cadastrar</button>
      </div>
    </form>
  </div>
  <div class="col-md-7">
    <div class="card">
      <div class="card-header">Meus credenciamentos</div>
      <div class="list-group list-group-flush">
        <?php while ($v = $rs->fetch_assoc()): ?>
          <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <div class="fw-semibold"><?=htmlspecialchars($v['nome'])?></div>
              <div class="small text-muted"><?=htmlspecialchars($v['endereco'].' - '.$v['cidade'].'/'.$v['uf'])?></div>
            </div>
            <a class="btn btn-outline-danger btn-sm" href="?del=<?=$v['id']?>" data-confirm="Excluir credenciamento?">Excluir</a>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__.'/../includes/footer.php'; ?>