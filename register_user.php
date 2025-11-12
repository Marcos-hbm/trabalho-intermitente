<?php
require __DIR__.'/includes/db.php';
require __DIR__.'/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $cpf = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $genero = trim($_POST['genero'] ?? '');
    $data_nascimento = $_POST['data_nascimento'] ?? null;
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Verifica CPF Registry (único entre user e company)
    if (strlen($cpf) !== 11) {
        $erro = 'CPF inválido (deve ter 11 dígitos).';
    } else {
        // Transação simples
        $mysqli->begin_transaction();
        try {
            // Checar duplicidade no cpf_registry
            $check = $mysqli->prepare("SELECT id FROM cpf_registry WHERE cpf = ?");
            $check->bind_param('s', $cpf);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                throw new Exception('CPF já utilizado por outro cadastro.');
            }

            $stmt = $mysqli->prepare("INSERT INTO users (nome, cpf, telefone, genero, data_nascimento, email, senha_hash) VALUES (?,?,?,?,?,?,?)");
            $stmt->bind_param('sssssss', $nome, $cpf, $telefone, $genero, $data_nascimento, $email, $senha_hash);
            $stmt->execute();
            $user_id = $stmt->insert_id;

            $ins = $mysqli->prepare("INSERT INTO cpf_registry (cpf, owner_type, owner_id) VALUES (?, 'user', ?)");
            $ins->bind_param('si', $cpf, $user_id);
            $ins->execute();

            $mysqli->commit();
            $_SESSION['user_id'] = $user_id;
            header('Location: /user/dashboard.php');
            exit;
        } catch (Exception $e) {
            $mysqli->rollback();
            $erro = $e->getMessage();
        }
    }
}
require __DIR__.'/includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-8 col-lg-7">
    <h2 class="mb-3">Cadastro - Usuário</h2>
    <?php if (!empty($erro)): ?><div class="alert alert-danger"><?=htmlspecialchars($erro)?></div><?php endif; ?>
    <form method="post" class="card card-body">
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Nome completo</label>
          <input type="text" class="form-control" name="nome" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">CPF</label>
          <input type="text" class="form-control" name="cpf" placeholder="Somente números" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Telefone</label>
          <input type="text" class="form-control" name="telefone">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Gênero</label>
          <input type="text" class="form-control" name="genero">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Data de nascimento</label>
          <input type="date" class="form-control" name="data_nascimento">
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
        <a class="btn btn-outline-secondary" href="/login_user.php">Já tenho conta</a>
      </div>
    </form>
  </div>
</div>
<?php require __DIR__.'/includes/footer.php'; ?>