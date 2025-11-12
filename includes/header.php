<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// base path dinâmico (ex: '/trabalho-intermitente/')
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Sistema Intermitente</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <base href="<?php echo htmlspecialchars($base); ?>">
  <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?php echo htmlspecialchars($base); ?>">Intermitente</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div id="navMain" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <?php if (!empty($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="user/dashboard.php">Jobs</a></li>
          <li class="nav-item"><a class="nav-link" href="user/companies.php">Empresas</a></li>
          <li class="nav-item"><a class="nav-link" href="user/profile.php">Perfil</a></li>
        <?php elseif (!empty($_SESSION['company_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="company/dashboard.php">Eventos</a></li>
          <li class="nav-item"><a class="nav-link" href="company/employees.php">Funcionários</a></li>
          <li class="nav-item"><a class="nav-link" href="company/venues.php">Credenciamentos</a></li>
          <li class="nav-item"><a class="nav-link" href="company/profile.php">Perfil</a></li>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav">
        <?php if (!empty($_SESSION['user_id']) || !empty($_SESSION['company_id'])): ?>
          <li class="nav-item"><a class="btn btn-light btn-sm" href="logout.php">Sair</a></li>
        <?php else: ?>
          <li class="nav-item me-2"><a class="btn btn-light btn-sm" href="login_user.php">Login Usuário</a></li>
          <li class="nav-item"><a class="btn btn-outline-light btn-sm" href="login_company.php">Login Empresa</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<main class="py-4">
<div class="container">