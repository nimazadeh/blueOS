<?php

use App\Core\Settings\SettingsService;

it('reads and writes settings through the service', function () {
    $service = app(SettingsService::class);

    $service->set('site.name', 'Blue Studio', 'string', true);

    expect($service->get('site.name'))->toBe('Blue Studio')
        ->and($service->getBool('site.enabled', false))->toBeFalse();
});

it('updates an existing setting without duplicating rows', function () {
    $service = app(SettingsService::class);

    $service->set('site.name', 'One');
    $service->set('site.name', 'Two');

    expect($service->get('site.name'))->toBe('Two')
        ->and(\App\Models\Settings::query()->where('key', 'site.name')->count())->toBe(1);
});
