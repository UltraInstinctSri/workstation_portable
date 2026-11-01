<?php
// ---------- Settings (edit these) ----------
const DB_HOST   = 'localhost';
const DB_NAME   = 'sis_login';
const DB_USER   = 'root';
const DB_PASS   = '';
const DEMO_MODE = true;   // true = show the OTP on screen. false = you must send it by email/SMS.

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

// ---------- Database ----------
function db() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die('Database connection failed. Check the settings in config.php and import database.sql.');
        }
    }
    return $pdo;
}

// ---------- Small helpers ----------
function e($s)        { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function old($k)      { return e($_POST[$k] ?? ''); }
function redirect($u) { header("Location: $u"); exit; }

function flash($msg)  { $_SESSION['flash'] = $msg; }
function get_flash()  { $m = $_SESSION['flash'] ?? ''; unset($_SESSION['flash']); return $m; }

function csrf_field() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return '<input type="hidden" name="csrf" value="' . $_SESSION['csrf'] . '">';
}
function check_csrf() {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) die('Invalid request. Go back and try again.');
}

// SHA-256 of (random salt + password)
function make_hash($salt, $password) { return hash('sha256', $salt . $password); }

// Send the user to login if they are not logged in as $type ('Student' or 'Faculty')
function require_login($type) {
    header('Cache-Control: no-store');
    if (($_SESSION['user_type'] ?? '') !== $type) redirect('login.php');
}

// ---------- Credential rules (same rules are repeated in validate.js for the browser) ----------
const COMMON_WORDS = ['abc', 'xyz', 'system', 'admin', 'root', 'india', 'qwerty', 'qerty', 'password', 'iloveyou', 'welcome'];

// Weak = contains a common word, or 3 digits in a row that repeat (222), rise (123) or fall (321)
function is_weak($s) {
    $low = strtolower($s);
    foreach (COMMON_WORDS as $w) if (strpos($low, $w) !== false) return true;
    for ($i = 0; $i + 2 < strlen($s); $i++) {
        $t = substr($s, $i, 3);
        if (!ctype_digit($t)) continue;
        [$a, $b, $c] = [(int)$t[0], (int)$t[1], (int)$t[2]];
        if (($a == $b && $b == $c) || ($b - $a == 1 && $c - $b == 1) || ($a - $b == 1 && $b - $c == 1)) return true;
    }
    return false;
}

// True if one string is inside the other (case-insensitive)
function overlap($a, $b) {
    $a = strtolower($a); $b = strtolower($b);
    return $a !== '' && $b !== '' && (strpos($a, $b) !== false || strpos($b, $a) !== false);
}

function validate_signup($uid, $username, $email, $pw, $pw2) {
    $err = [];

    // User ID
    if (!preg_match('/^[A-Za-z0-9]{10,}$/', $uid) || !preg_match('/[A-Z]/', $uid) || !preg_match('/[a-z]/', $uid) || !preg_match('/[0-9]/', $uid))
        $err[] = 'User ID: at least 10 letters/digits, with an uppercase letter, a lowercase letter and a digit.';
    if (strcasecmp($uid, $email) == 0 || overlap($uid, $username))
        $err[] = 'User ID cannot be your email or username, or part of them.';
    if (is_weak($uid))
        $err[] = 'User ID is too common (no 123 / 222 / 321 patterns or words like ABC, admin, root...).';

    // Username
    if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username))
        $err[] = 'Username: 3-30 letters, digits or underscore.';

    // Password
    if (strlen($pw) < 10)                    $err[] = 'Password must have at least 10 characters.';
    if (!preg_match('/[A-Z]/', $pw))         $err[] = 'Password needs an uppercase letter.';
    if (!preg_match('/[a-z]/', $pw))         $err[] = 'Password needs a lowercase letter.';
    if (!preg_match('/[0-9]/', $pw))         $err[] = 'Password needs a digit.';
    if (!preg_match('/[^A-Za-z0-9\s]/', $pw)) $err[] = 'Password needs a special character (@ # $ % & ! ...).';
    if (preg_match('/\s/', $pw))             $err[] = 'Password cannot contain spaces.';
    if ($pw !== $pw2)                        $err[] = 'Password and Confirm Password do not match.';
    if (is_weak($pw))                        $err[] = 'Password is too common (no 123 / 222 / 321 patterns or words like password, admin...).';
    if (overlap($uid, $pw) || overlap($username, $pw))
        $err[] = 'Password and User ID / username cannot contain each other.';

    return $err;
}

// ---------- Page layout ----------
function page_start($title) {
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">'
       . '<meta name="viewport" content="width=device-width, initial-scale=1">'
       . '<title>' . e($title) . '</title><link rel="stylesheet" href="style.css"></head><body><main class="card">';
}
function page_end() { echo '</main></body></html>'; }
