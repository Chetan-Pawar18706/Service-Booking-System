<?php
/**
 * Shared Authentication & Authorization Helper
 */

function getDbConnection() {
    include_once __DIR__ . '/../db.php';
    return $conn;
}

function checkUserLogin() {
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: ../login.php');
        exit();
    }
}

function checkAdminAccess() {
    checkUserLogin();
    if (($_SESSION['role'] ?? '') !== 'admin') {
        header('Location: ../index.php');
        exit();
    }
}

function checkUserAccess() {
    checkUserLogin();
    if (($_SESSION['role'] ?? '') !== 'user') {
        header('Location: ../index.php');
        exit();
    }
}

function checkProviderAccess() {
    checkUserLogin();
    if (($_SESSION['role'] ?? '') !== 'provider') {
        header('Location: ../index.php');
        exit();
    }
}

function redirectBasedOnRole() {
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        return;
    }
    
    $role = $_SESSION['role'] ?? 'user';
    
    switch ($role) {
        case 'admin':
            header('Location: admin/dashboard.php');
            break;
        case 'provider':
            header('Location: provider/dashboard.php');
            break;
        case 'user':
        default:
            header('Location: user/dashboard.php');
            break;
    }
    exit();
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUserRole() {
    return $_SESSION['role'] ?? 'user';
}

function getCurrentUsername() {
    return $_SESSION['username'] ?? '';
}

function isAdmin() {
    return getCurrentUserRole() === 'admin';
}

function isProvider() {
    return getCurrentUserRole() === 'provider';
}

function isRegularUser() {
    return getCurrentUserRole() === 'user';
}

function getFlashMessages() {
    $success = $_SESSION['flash_success'] ?? '';
    $error = $_SESSION['flash_error'] ?? '';
    unset($_SESSION['flash_success'], $_SESSION['flash_error']);
    return ['success' => $success, 'error' => $error];
}

function setFlashSuccess($message) {
    $_SESSION['flash_success'] = $message;
}

function setFlashError($message) {
    $_SESSION['flash_error'] = $message;
}
