<?php
// ... (código anterior)
require __DIR__.'/../includes/header.php';
?>
<h2>Meu Perfil</h2>
<?php if (!empty($ok)): ?><div class="alert alert-success">Dados atualizados.</div><?php endif; ?>
<div class="row">
  <div class="col-md-4">
    <div class="card">
      <?php if ($user['foto_path']): ?>
      <img src="<?=htmlspecialchars($user['foto_path'])?>" class="card-img-top" alt="Foto">
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
  <!-- restante do formulário -->
</div>
<?php require __DIR__.'/../includes/footer.php'; ?>