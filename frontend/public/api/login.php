<?php
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

    // Drupal's session write may not commit in this custom context, so we
    // persist the session data manually and set the cookie ourselves.
    $drupal_session = $_SESSION;
    $sid = bin2hex(random_bytes(32));
    $expires = time() + 14 * 86400;

    \Drupal::database()->merge('sessions')
        ->key('sid', $sid)
        ->fields([
            'uid' => $account->id(),
            'hostname' => $request->getClientIp() ?? '',
            'timestamp' => time(),
            'session' => serialize($drupal_session),
        ])
        ->execute();

    // The frontend runs under /api, but the CMS runs under /cms/web.
    // Drupal builds the session cookie name from the request base path,
    // so we must set the cookie name the CMS expects.
    $cookie_domain = session_get_cookie_params()['domain'];
    if ($cookie_domain) {
        $session_name_source = $cookie_domain;
    } else {
        $session_name_source = $request->getHost() . '/cms/web';
    }
    $session_name = ($request->isSecure() ? 'SSESS' : 'SESS') . substr(hash('sha256', $session_name_source), 0, 32);

    $params = session_get_cookie_params();
    $lifetime = $params['lifetime'] > 0 ? $params['lifetime'] : 14 * 86400;
    setcookie($session_name, $sid, time() + $lifetime, $params['path'], $params['domain'], $params['secure'], $params['httponly']);

    header('Location: /dashboard');
    exit;

} catch (Throwable $e) {
    error_log('Login error: ' . $e->getMessage());
    header('Location: /login?error=invalid_credentials');
    exit;
}
