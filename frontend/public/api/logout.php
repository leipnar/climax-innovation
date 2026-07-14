<?php
session_start();

try {
    $cms_path = '/home/u493946172/domains/climaxinnovation.com/public_html/cms';
    chdir($cms_path . '/web');

    $autoloader = require_once $cms_path . '/vendor/autoload.php';

    $request = Symfony\Component\HttpFoundation\Request::createFromGlobals();
    $kernel = Drupal\Core\DrupalKernel::createFromRequest($request, $autoloader, 'prod');
    $kernel->boot();
    $kernel->preHandle($request);

    if (!\Drupal::currentUser()->isAnonymous()) {
        user_logout();
    }
} catch (Throwable $e) {
    // Ignore bootstrap errors and fall through to clearing local cookies.
}

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

header('Location: /');
exit;
