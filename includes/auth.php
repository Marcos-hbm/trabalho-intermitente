<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedInUser(): bool {
    return isset($_SESSION['user_id']);
}

function isLoggedInCompany(): bool {
    return isset($_SESSION['company_id']);
}

function requireUser() {
    if (!isLoggedInUser()) {
        header('Location: /login_user.php?err=login');
        exit;
    }
}

function requireCompany() {
    if (!isLoggedInCompany()) {
        header('Location: /login_company.php?err=login');
        exit;
    }
}

function logoutAll() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}