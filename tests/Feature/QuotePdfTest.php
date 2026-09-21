<?php

use App\Models\Tender;
use App\Models\User;

test('owners can view draft quotation pdfs', function () {
    $user = User::factory()->create(['role' => 'user']);

    $tender = Tender::query()->create([
        'reference_no' => 'Q-1001',
        'title' => 'Test tender',
        'client_name' => 'Acme Ltd',
        'created_by_id' => $user->id,
        'status' => 'draft',
        'quote_status' => 'draft',
        'quoted_amount' => 1000,
        'currency' => 'USD',
    ]);

    $response = $this->actingAs($user)->get(route('quotes.pdf', ['tender' => $tender]));

    $response->assertStatus(200)
        ->assertHeader('Content-Type', 'application/pdf');
});
