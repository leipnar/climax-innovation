<?php
session_start();

if (empty($_SESSION['logged_in'])) {
    header('Location: /login');
    exit;
}

$uid = (int) $_SESSION['user_id'];
$destination = $_GET['destination'] ?? '/cms/web/admin';

if ($uid < 1) {
    header('Location: /login');
    exit;
}

$_SESSION = array();
$params = session_get_cookie_params();
setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
session_destroy();

$cms_path = '/home/u493946172/domains/climaxinnovation.com/public_html/cms';
chdir($cms_path . '/web');

$autoloader = require_once $cms_path . '/vendor/autoload.php';

$request = Symfony\Component\HttpFoundation\Request::createFromGlobals();
$kernel = Drupal\Core\DrupalKernel::createFromRequest($request, $autoloader, 'prod');
$kernel->boot();
$kernel->preHandle($request);

$account = \Drupal\user\Entity\User::load($uid);

if ($account && $account->isActive()) {
    user_login_finalize($account);

    $sid = session_id();
    $name = session_name();
    $params = session_get_cookie_params();
    setcookie($name, $sid, time() + $params['lifetime'], $params['path'], $params['domain'], $params['secure'], $params['httponly']);

    session_write_close();

    header('Location: ' . $destination);
} else {
    header('Location: /login?error=cms_unavailable');
}
exit;
