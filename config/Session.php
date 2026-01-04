<?php
class Session {
    public static function init() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    public static function get($key) {
        return $_SESSION[$key] ?? null;
    }
    
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public static function getUserRole() {
        return $_SESSION['user_role'] ?? null;
    }
    
    public static function isAdmin() {
        return self::getUserRole() === 'admin';
    }
    
    public static function destroy() {
        session_destroy();
    }
    
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: ../auth/login.php');
            exit();
        }
    }
    
    public static function requireAdmin() {
        self::requireLogin();
        if (!self::isAdmin()) {
            header('Location: ../public/index.php');
            exit();
        }
    }
}
?>