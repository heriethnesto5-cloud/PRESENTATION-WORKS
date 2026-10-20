<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function require_login(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . app_url('auth/login.php'));
        exit;
    }
}
function app_url(string $path = ''): string {
    $base = '/library_management_adminlte/';
    return $base . ltrim($path, '/');
}
function flash(string $message, string $type = 'success'): void {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}
function show_flash(): void {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        $type = in_array($f['type'], ['success','danger','warning','info']) ? $f['type'] : 'info';
        echo '<div class="alert alert-' . e($type) . ' alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>' . e($f['message']) . '</div>';
        unset($_SESSION['flash']);
    }
}
