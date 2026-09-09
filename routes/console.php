<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('blue:create-admin {email} {name}', function () {
    $email = (string) $this->argument('email');
    $name = (string) $this->argument('name');

    $password = $this->secret('Password (min 12 characters, shown only here)');

    if (! is_string($password) || strlen($password) < 12) {
        $this->error('Password must be at least 12 characters.');

        return self::FAILURE;
    }

    $user = \App\Models\User::query()->updateOrCreate(
        ['email' => $email],
        [
            'name' => $name,
            'password' => $password,
            'locale' => $this->choice('Locale', ['en', 'fa'], 'en'),
            'is_active' => true,
            'email_verified_at' => now(),
        ],
    );

    $this->info(sprintf('Admin user ready: %s (%s)', $user->email, $user->name));

    return self::SUCCESS;
})->purpose('Create the Blue Control administrator account (manual credentials, real data only)');
