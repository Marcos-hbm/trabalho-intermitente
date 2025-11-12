<?php
// Projeto: Sistema Intermitente
// Arquivo de configuração central com BASE_PATH dinâmico
// Defina BASE_PATH como o caminho relativo da aplicação (ex: '/trabalho-intermitente/') ou automatique
if (!defined('BASE_PATH')) {
    // calcula base a partir do SCRIPT_NAME (ex: /trabalho-intermitente/index.php -> /trabalho-intermitente/)
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\') . '/';
    define('BASE_PATH', $base);
}

// configurações adicionais (DB etc) podem ser colocadas aqui no futuro
?>