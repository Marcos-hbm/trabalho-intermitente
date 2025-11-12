<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';

// Permite visualização via empresa; ou preview simpels
$uid = intval($_GET['id'] ?? 0);
$preview_event = intval($_GET['event_id'] ?? 0);

if ($uid > 0) {
    requireCompany();
    $stmt = $mysqli->prepare("SELECT nome, cpf, telefone, email, genero, data_nascimento, foto_path FROM users WHERE id=?");
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
} elseif ($preview_event > 0) {
    // Apenas uma página de informação sobre o evento ao usuário
    $stmt = $mysqli->prepare("SELECT e.nome, e.descricao, c.nome AS empresa FROM events e JOIN companies c ON c.id=e.company_id WHERE e.id=?");
    $stmt->bind_param('i', $preview_event);
    $stmt->execute();
    $ev = $stmt->get_result()->fetch_assoc();
} else {
    header('Location: /');
    exit;
}

require __DIR__.'/../includes/header.php';
?>

<?php if (!empty($user)): ?>
  <h2>Perfil do Funcionário</h2>
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
          <p class="mb-1"><strong>Email:</strong> <?=htmlspecialchars($user['email'])?></p>
          <p class="mb-1"><strong>Telefone:</strong> <?=htmlspecialchars($user['telefone'])?></p>
          <p class="mb-1"><strong>Gênero:</strong> <?=htmlspecialchars($user['genero'])?></p>
          <p class="mb-1"><strong>Nasc.:</strong> <?=htmlspecialchars(date('d/m/Y', strtotime($user['data_nascimento'])))?></p>
        </div>
      </div>
    </div>
  </div>
<?php elseif (!empty($ev)): ?>
  <h2><?=htmlspecialchars($ev['nome'])?></h2>
  <p class="text-muted">Empresa: <?=htmlspecialchars($ev['empresa'])?></p>
  <p><?=nl2br(htmlspecialchars($ev['descricao']))?></p>
  <a class="btn btn-outline-secondary" href="/user/dashboard.php">Voltar</a>
<?php endif; ?>

<?php require __DIR__.'/../includes/footer.php'; ?>