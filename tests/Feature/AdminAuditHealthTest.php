<?php

use App\Models\User;

test('admin audit page surfaces failed login anomalies and health signals', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $logPath = storage_path('logs/laravel.log');
    $logEntry = now()->toDateTimeString().' [warning] Failed login attempt for admin@example.com. Too many attempts detected.' . PHP_EOL;
    file_put_contents($logPath, $logEntry, FILE_APPEND);

    $response = $this->actingAs($admin)->get(route('admin.settings'));

    $response->assertOk()
        ->assertSee('Audit & health')
        ->assertSee('Potential login anomalies')
        ->assertSee('Failed login');
});
