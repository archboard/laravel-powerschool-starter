<?php

use Symfony\Component\Process\Process;

/*
|--------------------------------------------------------------------------
| Smoke Tests
|--------------------------------------------------------------------------
|
| These tests spin up a dedicated artisan serve instance backed by the
| saas_test PostgreSQL database and assert that pages render without any
| JavaScript errors or unexpected console output.
|
| Run with: php artisan test --testsuite=Browser
|
*/

$serverProcess = null;

beforeAll(function () use (&$serverProcess) {
    $root = dirname(__DIR__, 2);

    // Wipe and rebuild the test database schema
    Process::fromShellCommandline('php artisan migrate:fresh --force', $root)->mustRun();

    // Start a dedicated test server. SESSION_DRIVER=cookie is set inline so it
    // overrides the SESSION_DRIVER=array that phpunit injects into the environment,
    // allowing Playwright's browser to maintain a real cookie-based session.
    $serverProcess = Process::fromShellCommandline(
        'SESSION_DRIVER=cookie php artisan serve --host=127.0.0.1 --port=8001',
        $root,
    );
    $serverProcess->start();

    // Allow the server a moment to bind to the port before tests run
    usleep(1_000_000);
});

afterAll(function () use (&$serverProcess) {
    $serverProcess?->stop();
});

it('public pages load without js errors', function () {
    visit(['/login', '/forgot-password'])->assertNoSmoke();
});
