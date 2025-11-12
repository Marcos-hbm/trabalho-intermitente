<?php
// Configuração de conexão com o banco (ajuste conforme seu ambiente)
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'tcc_intermitente';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die('Erro na conexão com o banco: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');