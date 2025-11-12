<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
requireUser();

$uid = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telefone = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $nova_senha = $_POST['nova_senha'] ?? '';

    // Upload foto
    $foto_path = null;
    if (!empty($_FILES['foto']['name'])) {
        @mkdir(__DIR__.'/../uploads/users', 0775, true);
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $fname = 'uploads/users/u'.$uid.'_'.time().'.'.strtolower($ext);
        if (move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__.'/../'.$fname)) {
            $foto_path = $fname;
        }
    }

    if ($foto_path) {
        $stmt = $mysqli->prepare("UPDATE users SET telefone=?, email=?, foto_path=? WHERE id=?");
        $stmt->bind_param('sssi', $telefone, $email, $foto_path, $uid);
    } else {
        $stmt = $mysqli->prepare("UPDATE users SET telefone=?, email=? WHERE id=?");
        $stmt->bind_param('ssi', $telefone, $email, $uid);
    }
    $stmt->execute();

    if ($nova_senha) {
        $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $stmt2 = $mysqli->prepare("UPDATE users SET senha_hash=? WHERE id=?");
        $stmt2->bind_param('si', $hash, $uid);
        $stmt2->execute();
    }

    $ok = true;
}

$stmt = $mysqli->prepare("SELECT nome, cpf, telefone, genero, data_nascimento, email, foto_path FROM users WHERE id=?");
$stmt->bind_param('i', $uid);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

require __DIR__.'/../includes/header.php';
?>
<h2>Meu Perfil</h2>
<?php if (!empty($ok)): ?><div class="alert alert-success">Dados atualizados.</div><?php endif; ?>
<div class="row">
  <div class="col-md-4">
    <div class="card">
      <?php if ($user['foto_path']): ?>
      <img src="/<?=htmlspecialchars($user['foto_path'])?>" class="card-img-top" alt="Foto">
      <?php else: ?>
      <img src="https://i.pravatar.cc/400?u=<?=$uid?>" class="card-img-top" alt="">
      <?php endif; ?>
      <div class="card-body">
        <h5><?=htmlspecialchars($user['nome'])?></h5>
        <p class="mb-1"><strong>CPF:</strong> <?=htmlspecialchars($user['cpf'])?></p>
        <p class="mb-1"><strong>Gênero:</strong> <?=htmlspecialchars($user['genero'])?></p>
        <p class="mb-1"><strong>Nasc.:</strong> <?=htmlspecialchars(date('d/m/Y', strtotime($user['data_nascimento'])))?></p>
      </div>
    </div>
  </div>
  <div class="col-md-8">
    <form method="post" enctype="multipart/form-data" class="card card-body">
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Telefone</label>
          <input type="text" class="form-control" name="telefone" value="<?=htmlspecialchars($user['telefone'])?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">E-mail</label>
          <input type="email" class="form-control" name="email" value="<?=htmlspecialchars($user['email'])?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Nova senha</label>
          <input type="password" class="form-control" name="nova_senha" placeholder="Opcional">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Foto</label>
          <input type="file" class="form-control" name="foto" accept="image/*">
        </div>
      </div>
      <div class="d-flex justify-content-between">
        <button class="btn btn-primary">Salvar</button>
        <a class="btn btn-outline-danger" href="/user/delete_account.php" data-confirm="Tem certeza que deseja excluir sua conta?">Excluir conta</a>
      </div>
    </form>
  </div>
</div>
<?php require __DIR__.'/../includes/footer.php'; ?>