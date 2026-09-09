<?php

use App\Core\Slug\SlugService;

it('slugifies plain text into a kebab-case slug', function () {
    $service = new SlugService();

    expect($service->slugify('Agentflow X'))->toBe('agentflow-x')
        ->and($service->slugify('  Build  Real-time   Dashboards  '))->toBe('build-real-time-dashboards');
});

it('transliterates unicode characters when possible', function () {
    $service = new SlugService();

    expect($service->slugify('Signal — نرم‌افزار'))->toBeString()
        ->and($service->slugify('Signal — نرم‌افزار'))->not->toBeEmpty();
});

it('never returns an empty slug', function () {
    $service = new SlugService();

    expect($service->slugify(''))->toBe('item')
        ->and($service->slugify('---'))->toBeString()
        ->and($service->slugify('---'))->not->toBeEmpty();
});
