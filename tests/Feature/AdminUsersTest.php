<?php

use App\Livewire\AdminUsers;
use App\Models\User;
use Livewire\Livewire;

test('admin can permanently delete another user', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();

    Livewire::actingAs($admin)
        ->test(AdminUsers::class)
        ->call('deleteUser', $user->id)
        ->assertHasNoErrors();

    expect(User::query()->find($user->id))->toBeNull();
});

test('admin cannot delete their own account', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test(AdminUsers::class)
        ->call('deleteUser', $admin->id)
        ->assertForbidden();

    expect(User::query()->find($admin->id))->not->toBeNull();
});