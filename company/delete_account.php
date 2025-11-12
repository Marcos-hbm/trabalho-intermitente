<?php
require __DIR__.'/../includes/db.php';
require __DIR__.'/../includes/auth.php';
requireCompany();

$cid = $_SESSION['company_id'];

// Se empresa cadastrou com CPF, limpar cpf_registry
$getdoc = $mysqli->prepare("SELECT cpf_cnpj FROM companies WHERE id=?");
$getdoc->bind_param('i', $cid);
$getdoc->execute();
$getdoc->bind_result($doc);
$getdoc->fetch();
$getdoc->close();

$doc = preg_replace('/\D/','',$doc);
if (strlen($doc) === 11) {
    $delreg = $mysqli->prepare("DELETE FROM cpf_registry WHERE owner_type='company' AND owner_id=?");
    $delreg->bind_param('i', $cid);
    $delreg->execute();
}

// Remoção cascata
$del = $mysqli->prepare("DELETE FROM companies WHERE id=?");
$del->bind_param('i', $cid);
$del->execute();

logoutAll();
header('Location: /?msg=Conta+excluida');