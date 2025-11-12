<?php
// ... (código anterior de processamento)
        $mysqli->commit();
        $_SESSION['company_id'] = $company_id;
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';
        header('Location: ' . $base . 'company/dashboard.php');
        exit;
// ... (restante do arquivo)
require __DIR__.'/includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-8 col-lg-7">
    <h2 class="mb-3">Cadastro - Empresa</h2>
    <?php if (!empty($erro)): ?><div class="alert alert-danger"><?=htmlspecialchars($erro)?></div><?php endif; ?>
    <form method="post" class="card card-body">
      <!-- campos -->
      <div class="d-flex justify-content-between">
        <button class="btn btn-success">Cadastrar</button>
        <a class="btn btn-outline-secondary" href="login_company.php">Já tenho conta</a>
      </div>
    </form>
  </div>
</div>
<?php require __DIR__.'/includes/footer.php'; ?>