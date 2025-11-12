<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
requireCompany();

$cid = $_SESSION['company_id'];
$user_id = intval($_GET['user_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $user_id = intval($_POST['user_id']);
    $texto = trim($_POST['message'] ?? '');
    if ($texto !== '' && $user_id > 0) {
        $stmt = $mysqli->prepare("INSERT INTO messages (company_id, user_id, message_text) VALUES (?,?,?)");
        $stmt->bind_param('iis', $cid, $user_id, $texto);
        $stmt->execute();
        $ok = true;
    }
}

$u = null;
if ($user_id > 0) {
    $stmt = $mysqli->prepare("SELECT id, nome, email FROM users WHERE id=?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $u = $stmt->get_result()->fetch_assoc();
}

require __DIR__.'/../includes/header.php';
?>
<h2>Mensagem ao Funcionário</h2>
<?php if (!empty($ok)): ?><div class="alert alert-success">Mensagem enviada.</div><?php endif; ?>
<?php if ($u): ?>
  <div class="card mb-3">
    <div class="card-body">
      <div class="fw-semibold"><?=htmlspecialchars($u['nome'])?></div>
      <div class="small text-muted"><?=htmlspecialchars($u['email'])?></div>
    </div>
  </div>
  <form method="post" class="card card-body">
    <input type="hidden" name="user_id" value="<?=$u['id']?>">
    <div class="mb-3">
      <label class="form-label">Mensagem</label>
      <textarea class="form-control" name="message" rows="5" placeholder="Escreva sua mensagem..." required></textarea>
    </div>
    <div class="d-flex gap-2">
      <a href="/company/employees.php" class="btn btn-outline-secondary">Voltar</a>
      <button class="btn btn-primary">Enviar</button>
    </div>
  </form>
<?php else: ?>
  <div class="alert alert-warning">Funcionário não encontrado.</div>
  <a href="/company/employees.php" class="btn btn-outline-secondary">Voltar</a>
<?php endif; ?>
<?php require __DIR__.'/../includes/footer.php'; ?>