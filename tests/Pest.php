<?php

/*
|--------------------------------------------------------------------------
| Pest configuration
|--------------------------------------------------------------------------
|
| Feature tests refresh the database (MySQL in CI, in-memory SQLite locally).
| Unit tests stay database-free.
|
*/

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->in('Feature');

uses(Tests\TestCase::class)->in('Feature', 'Unit');
