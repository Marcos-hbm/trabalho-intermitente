<?php
// register_user.php
// Processa cadastro de usuário e exibe o formulário.

require __DIR__.'/includes/db.php';     // garante $mysqli
require __DIR__.'/includes/auth.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $cpf = preg_replace('/\D+/', '', $_POST['cpf'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $genero = trim($_POST['genero'] ?? '');
    $data_nascimento = trim($_POST['data_nascimento'] ?? null);
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($nome === '' || $cpf === '' || $email === '' || $senha === '') {
        $erro = 'Preencha os campos obrigatórios.';
    } else {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        // Inicia transação
        $mysqli->begin_transaction();
        try {
            // Verifica exclusividade de CPF (se houver)
            if (strlen($cpf) === 11) {
                $check = $mysqli->prepare("SELECT id FROM cpf_registry WHERE cpf = ?");
                if (!$check) throw new Exception('Erro no banco (check cpf).');
                $check->bind_param('s', $cpf);
                $check->execute();
                $res = $check->get_result();
                if ($res && $res->num_rows > 0) {
                    throw new Exception('CPF já utilizado por outro cadastro.');
                }
            }

            $stmt = $mysqli->prepare("INSERT INTO users (nome, cpf, telefone, genero, data_nascimento, email, senha_hash) VALUES (?,?,?,?,?,?,?)");
            if (!$stmt) throw new Exception('Erro no banco (insert user).');
            $stmt->bind_param('sssssss', $nome, $cpf, $telefone, $genero, $data_nascimento, $email, $senha_hash);
            $stmt->execute();
            $user_id = $stmt->insert_id;

            // Registra CPF na tabela de registro (se aplicável)
            if (strlen($cpf) === 11) {
                $ins = $mysqli->prepare("INSERT INTO cpf_registry (cpf, owner_type, owner_id) VALUES (?, 'user', ?)");
                if (!$ins) throw new Exception('Erro no banco (insert cpf_registry).');
                $ins->bind_param('si', $cpf, $user_id);
                $ins->execute();
            }

            $mysqli->commit();

            // Login automático
            $_SESSION['user_id'] = $user_id;

            // Redirect relativo
            header('Location: user/dashboard.php');
            exit;
        } catch (Exception $e) {
            if (isset($mysqli) && $mysqli) {
                $mysqli->rollback();
            }
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
          <input type="text" class="form-control" name="nome" required value="<?=htmlspecialchars($_POST['nome'] ?? '')?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">CPF</label>
          <input type="text" class="form-control" name="cpf" placeholder="Somente números" required value="<?=htmlspecialchars($_POST['cpf'] ?? '')?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Telefone</label>
          <input type="text" class="form-control" name="telefone" value="<?=htmlspecialchars($_POST['telefone'] ?? '')?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Gênero</label>
          <input type="text" class="form-control" name="genero" value="<?=htmlspecialchars($_POST['genero'] ?? '')?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Data de nascimento</label>
          <input type="date" class="form-control" name="data_nascimento" value="<?=htmlspecialchars($_POST['data_nascimento'] ?? '')?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">E-mail</label>
          <input type="email" class="form-control" name="email" required value="<?=htmlspecialchars($_POST['email'] ?? '')?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Senha</label>
          <input type="password" class="form-control" name="senha" required>
        </div>
      </div>
      <div class="d-flex justify-content-between">
        <button class="btn btn-success">Cadastrar</button>
        <a class="btn btn-outline-secondary" href="login_user.php">Já tenho conta</a>
      </div>
    </form>
  </div>
</div>
<?php require __DIR__.'/includes/footer.php'; ?>