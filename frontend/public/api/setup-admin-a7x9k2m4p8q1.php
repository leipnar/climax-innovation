<?php
// Temporary one-time admin setup script for Climax Innovation handover.
// This file should be deleted immediately after use.

$expected_token = 'sZmZkStt-SJXlm7edPz7QICLtF4wqCxdtNWZWtMeU1c';

header('Content-Type: application/json');

$token = $_GET['token'] ?? '';
if ($token !== $expected_token) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
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

    $username = 'climaxadmin';
    $email = 'admin@climaxinnovation.com';
    $password = base64_encode(random_bytes(18)); // 24 chars

    $user_storage = \Drupal::entityTypeManager()->getStorage('user');
    $users = $user_storage->loadByProperties(['name' => $username]);

    if (!empty($users)) {
        $user = reset($users);
    } else {
        $user = $user_storage->create([
            'name' => $username,
            'mail' => $email,
            'status' => 1,
            'roles' => ['administrator'],
        ]);
    }

    $user->setPassword($password);
    $user->addRole('administrator');
    $user->save();

    echo json_encode([
        'success' => true,
        'username' => $username,
        'password' => $password,
        'login_url' => 'https://climaxinnovation.com/login',
        'cms_url' => 'https://climaxinnovation.com/cms/web/admin',
        'note' => 'Keep these credentials safe. Change the password after first login.',
    ], JSON_PRETTY_PRINT);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Setup failed', 'message' => $e->getMessage()]);
}
