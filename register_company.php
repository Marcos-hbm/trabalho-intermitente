<?php
require __DIR__.'/includes/db.php';
require __DIR__.'/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $doc = preg_replace('/\D/', '', $_POST['cpf_cnpj'] ?? ''); // CPF ou CNPJ
    $telefone = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $mysqli->begin_transaction();
    try {
        // Se for CPF (11), garantir regra de exclusividade
        if (strlen($doc) === 11) {
            $check = $mysqli->prepare("SELECT id FROM cpf_registry WHERE cpf = ?");
            $check->bind_param('s', $doc);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                throw new Exception('Este CPF já está vinculado a um cadastro de usuário/empresa.');
            }
        }

        $stmt = $mysqli->prepare("INSERT INTO companies (nome, cpf_cnpj, telefone, email, senha_hash) VALUES (?,?,?,?,?)");
        $stmt->bind_param('sssss', $nome, $doc, $telefone, $email, $senha_hash);
        $stmt->execute();
        $company_id = $stmt->insert_id;

        if (strlen($doc) === 11) {
            $ins = $mysqli->prepare("INSERT INTO cpf_registry (cpf, owner_type, owner_id) VALUES (?, 'company', ?)");
            $ins->bind_param('si', $doc, $company_id);
            $ins->execute();
        }

        $mysqli->commit();
        $_SESSION['company_id'] = $company_id;
        header('Location: /company/dashboard.php');
        exit;
    } catch (Exception $e) {
        $mysqli->rollback();
        $erro = $e->getMessage();
    }
}
require __DIR__.'/includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-8 col-lg-7">
    <h2 class="mb-3">Cadastro - Empresa</h2>
    <?php if (!empty($erro)): ?><div class="alert alert-danger"><?=htmlspecialchars($erro)?></div><?php endif; ?>
    <form method="post" class="card card-body">
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Nome completo</label>
          <input type="text" class="form-control" name="nome" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">CPF/CNPJ</label>
          <input type="text" class="form-control" name="cpf_cnpj" placeholder="Somente números" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Telefone</label>
          <input type="text" class="form-control" name="telefone">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">E-mail</label>
          <input type="email" class="form-control" name="email" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Senha</label>
          <input type="password" class="form-control" name="senha" required>
        </div>
      </div>
      <div class="d-flex justify-content-between">
        <button class="btn btn-success">Cadastrar</button>
        <a class="btn btn-outline-secondary" href="/login_company.php">Já tenho conta</a>
      </div>
    </form>
  </div>
</div>
<?php require __DIR__.'/includes/footer.php'; ?>