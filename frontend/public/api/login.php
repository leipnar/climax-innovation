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
    $cms_path = '/home/u493946172/domains/climaxinnovation.com/public_html/cms';
    chdir($cms_path . '/web');

    $autoloader = require_once $cms_path . '/vendor/autoload.php';

    $request = Symfony\Component\HttpFoundation\Request::createFromGlobals();
    $kernel = Drupal\Core\DrupalKernel::createFromRequest($request, $autoloader, 'prod');
    $kernel->boot();
    $kernel->preHandle($request);

    $users = \Drupal::entityTypeManager()->getStorage('user')->loadByProperties(['mail' => $email]);
    $account = $users ? reset($users) : null;

    if (!$account || !$account->isActive()) {
        header('Location: /login?error=invalid_credentials');
        exit;
    }

    $password_hash = $account->getPassword();
    $valid = \Drupal::service('password')->check($password, $password_hash);

    if (!$valid) {
        header('Location: /login?error=invalid_credentials');
        exit;
    }

    user_login_finalize($account);

    $sid = session_id();
    $name = session_name();
    $params = session_get_cookie_params();
    setcookie($name, $sid, time() + $params['lifetime'], $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    session_write_close();

    header('Location: /dashboard');
    exit;

} catch (Throwable $e) {
    error_log('Login error: ' . $e->getMessage());
    header('Location: /login?error=invalid_credentials');
    exit;
}
