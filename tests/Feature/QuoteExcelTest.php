<?php

use App\Models\Tender;
use App\Models\User;

test('owners can download excel quotations', function () {
    $user = User::factory()->create(['role' => 'user']);

    $tender = Tender::query()->create([
        'reference_no' => 'Q-1002',
        'title' => 'Excel tender',
        'client_name' => 'Acme Ltd',
        'created_by_id' => $user->id,
        'status' => 'draft',
        'quote_status' => 'sent',
        'quoted_amount' => 2500,
        'currency' => 'USD',
    ]);

    $tender->quoteItems()->create([
        'description' => 'Consulting',
        'unit' => 'hour',
        'quantity' => 5,
        'unit_price' => 100,
        'version' => 1,
        'vat_rate' => 18,
    ]);

    $response = $this->actingAs($user)->get(route('quotes.excel', ['tender' => $tender]));

    $response->assertStatus(200)
        ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});
