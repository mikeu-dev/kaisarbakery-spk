<?php

test('root redirects to panel login', function () {
    $response = $this->get('/');

    $response->assertRedirect('/panel');

    $panel = $this->get('/panel/login');
    $panel->assertOk();
});
