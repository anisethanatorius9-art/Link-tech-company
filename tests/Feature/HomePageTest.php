<?php

test('home page displays the business branding and value proposition', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('Ventis POS')
        ->assertSee('Made for modern retail');
});
