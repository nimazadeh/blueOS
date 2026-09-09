<?php

use App\Core\Seo\MetaResolver;

it('appends the site suffix to the title', function () {
    $meta = (new MetaResolver())->resolve('Blue Studio OS');

    expect($meta->title)->toContain('Blue Studio OS')
        ->and($meta->title)->toContain('Blue Studio');
});

it('falls back to default description when none provided', function () {
    $meta = (new MetaResolver())->resolve('Test');

    expect($meta->description)->not->toBeEmpty()
        ->and(strlen($meta->description))->toBeLessThanOrEqual(160);
});

it('respects explicit overrides', function () {
    $meta = (new MetaResolver())->resolve('Test', [
        'description' => 'Explicit description.',
        'robots' => 'noindex,follow',
        'canonical' => 'https://example.com/test',
    ]);

    expect($meta->description)->toBe('Explicit description.')
        ->and($meta->robots)->toBe('noindex,follow')
        ->and($meta->canonical)->toBe('https://example.com/test');
});

it('produces a serializable array with social defaults', function () {
    $meta = (new MetaResolver())->resolve('Serialize me');

    expect($meta->toArray())->toHaveKeys([
        'title', 'description', 'canonical', 'robots', 'og_type',
        'og_title', 'og_description', 'og_image', 'twitter_card',
        'twitter_title', 'twitter_description', 'twitter_image',
    ])->and($meta->toArray()['og_title'])->toBe($meta->title);
});
