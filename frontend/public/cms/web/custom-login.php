<?php
// Custom login handler placed inside the Drupal web root so Drupal can set
// the CMS session cookie with the correct name and write the session itself.

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
    $autoloader = require_once '/home/u493946172/domains/climaxinnovation.com/public_html/cms/vendor/autoload.php';

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

    $valid = \Drupal::service('password')->check($password, $account->getPassword());

    if (!$valid) {
        header('Location: /login?error=invalid_credentials');
        exit;
    }

    user_login_finalize($account);

    // Start the session now so the CMS session cookie is sent to the browser.
    \Drupal::service('session_manager')->save();

    header('Location: /dashboard');
    exit;

} catch (Throwable $e) {
    error_log('Custom CMS login error: ' . $e->getMessage());
    header('Location: /login?error=invalid_credentials');
    exit;
}
