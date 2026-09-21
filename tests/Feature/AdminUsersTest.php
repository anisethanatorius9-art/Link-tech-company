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

test('users table paginates and sorts only approved columns', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    User::factory()->count(11)->create();

    Livewire::actingAs($admin)
        ->test(AdminUsers::class)
        ->assertSee('12 accounts')
        ->call('sort', 'name')
        ->assertSet('sortBy', 'name')
        ->assertSet('sortDirection', 'asc')
        ->call('sort', 'not_a_column')
        ->assertSet('sortBy', 'name')
        ->assertSet('sortDirection', 'asc');
});