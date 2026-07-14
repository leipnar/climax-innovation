<?php
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /login');
    exit;
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    header('Location: /login?error=invalid_credentials');
    exit;
}

try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=u493946172_drupal;charset=utf8mb4',
        'u493946172_drupal',
        'jad3kba_vha-RPF!aqe',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    $stmt = $pdo->prepare("
        SELECT u.uid, u.name, u.mail, u.pass, u.status
        FROM drupal_users_field_data u
        WHERE u.mail = ? AND u.status = 1
        LIMIT 1
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        header('Location: /login?error=invalid_credentials');
        exit;
    }
    
    $password_hash = $user['pass'];
    
    if (password_verify($password, $password_hash) || 
        (strlen($password_hash) === 55 && substr($password_hash, 0, 4) === '$S$D' && _drupal_check_password($password, $password_hash))) {
        
        $_SESSION['user_id'] = $user['uid'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['mail'];
        $_SESSION['logged_in'] = true;
        
        session_regenerate_id(true);
        
        header('Location: /dashboard');
        exit;
    } else {
        header('Location: /login?error=invalid_credentials');
        exit;
    }
    
} catch (PDOException $e) {
    error_log('Login error: ' . $e->getMessage());
    header('Location: /login?error=invalid_credentials');
    exit;
}

function _drupal_check_password($password, $hash) {
    if (substr($hash, 0, 2) == 'U$') {
        $hash = substr($hash, 1);
        $password = mb_convert_encoding($password, 'UTF-16LE');
    }
    
    $setting = substr($hash, 0, 12);
    if ($setting[0] != '$' || $setting[2] != '$') {
        return false;
    }
    
    $count_log2 = strpos('./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz', $setting[3]);
    if ($count_log2 < 7 || $count_log2 > 30) {
        return false;
    }
    
    $salt = substr($setting, 4, 8);
    if (strlen($salt) != 8) {
        return false;
    }
    
    $count = 1 << $count_log2;
    $hash = hash('sha512', $salt . $password, true);
    do {
        $hash = hash('sha512', $hash . $password, true);
    } while (--$count);
    
    $expected = substr($setting, 12) . substr(base64_encode($hash), 0, 43);
    $expected = strtr($expected, array('+' => '.', '=' => ''));
    
    return $hash === $expected || substr(base64_encode($hash), 0, 55) === substr($expected, 0, 55);
}
