<?php

it('reports a healthy application with an up database', function () {
    $this->getJson(route('health'))
        ->assertOk()
        ->assertJsonPath('status', 'ok')
        ->assertJsonPath('database', 'up');
});
