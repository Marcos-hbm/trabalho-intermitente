<?php
require __DIR__.'/includes/db.php';
require __DIR__.'/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $stmt = $mysqli->prepare("SELECT id, senha_hash FROM companies WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($cid, $hash);
    if ($stmt->fetch() && password_verify($senha, $hash)) {
        $_SESSION['company_id'] = $cid;
        header('Location: /company/dashboard.php');
        exit;
    }
    $erro = 'Credenciais inválidas.';
    $stmt->close();
}
require __DIR__.'/includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <h2 class="mb-3">Login - Empresa</h2>
    <?php if (!empty($_GET['err']) && $_GET['err']==='login'): ?>
      <div class="alert alert-warning">Faça login para continuar.</div>
    <?php endif; ?>
    <?php if (!empty($erro)): ?><div class="alert alert-danger"><?=htmlspecialchars($erro)?></div><?php endif; ?>
    <form method="post" class="card card-body">
      <div class="mb-3">
        <label class="form-label">E-mail</label>
        <input type="email" class="form-control" name="email" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Senha</label>
        <input type="password" class="form-control" name="senha" required>
      </div>
      <div class="d-flex justify-content-between">
        <button class="btn btn-primary">Entrar</button>
        <a class="btn btn-outline-secondary" href="/register_company.php">Criar conta</a>
      </div>
    </form>
  </div>
</div>
<?php require __DIR__.'/includes/footer.php'; ?>