<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
requireCompany();

$cid = $_SESSION['company_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telefone = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $nova_senha = $_POST['nova_senha'] ?? '';
    $accept = isset($_POST['accepting_affiliation']) ? 1 : 0;

    // Upload foto
    $foto_path = null;
    if (!empty($_FILES['foto']['name'])) {
        @mkdir(__DIR__.'/../uploads/companies', 0775, true);
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $fname = 'uploads/companies/c'.$cid.'_'.time().'.'.strtolower($ext);
        if (move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__.'/../'.$fname)) {
            $foto_path = $fname;
        }
    }

    if ($foto_path) {
        $stmt = $mysqli->prepare("UPDATE companies SET telefone=?, email=?, foto_path=?, accepting_affiliation=? WHERE id=?");
        $stmt->bind_param('sssii', $telefone, $email, $foto_path, $accept, $cid);
    } else {
        $stmt = $mysqli->prepare("UPDATE companies SET telefone=?, email=?, accepting_affiliation=? WHERE id=?");
        $stmt->bind_param('ssii', $telefone, $email, $accept, $cid);
    }
    $stmt->execute();

    if ($nova_senha) {
        $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $stmt2 = $mysqli->prepare("UPDATE companies SET senha_hash=? WHERE id=?");
        $stmt2->bind_param('si', $hash, $cid);
        $stmt2->execute();
    }

    $ok = true;
}

$stmt = $mysqli->prepare("SELECT nome, cpf_cnpj, telefone, email, foto_path, accepting_affiliation FROM companies WHERE id=?");
$stmt->bind_param('i', $cid);
$stmt->execute();
$comp = $stmt->get_result()->fetch_assoc();

require __DIR__.'/../includes/header.php';
?>
<h2>Perfil da Empresa</h2>
<?php if (!empty($ok)): ?><div class="alert alert-success">Dados atualizados.</div><?php endif; ?>
<div class="row">
  <div class="col-md-4">
    <div class="card">
      <?php if ($comp['foto_path']): ?>
      <img src="/<?=htmlspecialchars($comp['foto_path'])?>" class="card-img-top" alt="Logo">
      <?php else: ?>
      <img src="https://picsum.photos/seed/comp<?=$cid?>/500/300" class="card-img-top" alt="">
      <?php endif; ?>
      <div class="card-body">
        <h5><?=htmlspecialchars($comp['nome'])?></h5>
        <p class="mb-1"><strong>CPF/CNPJ:</strong> <?=htmlspecialchars($comp['cpf_cnpj'])?></p>
      </div>
    </div>
  </div>
  <div class="col-md-8">
    <form method="post" enctype="multipart/form-data" class="card card-body">
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Telefone</label>
          <input type="text" class="form-control" name="telefone" value="<?=htmlspecialchars($comp['telefone'])?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">E-mail</label>
          <input type="email" class="form-control" name="email" value="<?=htmlspecialchars($comp['email'])?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Nova senha</label>
          <input type="password" class="form-control" name="nova_senha" placeholder="Opcional">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Foto/Logo</label>
          <input type="file" class="form-control" name="foto" accept="image/*">
        </div>
        <div class="col-12 mb-3 form-check">
          <input class="form-check-input" type="checkbox" name="accepting_affiliation" id="aff" <?=$comp['accepting_affiliation']?'checked':''?>>
          <label class="form-check-label" for="aff">Empresa oferece filiação</label>
        </div>
      </div>
      <div class="d-flex justify-content-between">
        <button class="btn btn-primary">Salvar</button>
        <a class="btn btn-outline-danger" href="/company/delete_account.php" data-confirm="Tem certeza que deseja excluir sua conta?">Excluir conta</a>
      </div>
    </form>
  </div>
</div>
<?php require __DIR__.'/../includes/footer.php'; ?>