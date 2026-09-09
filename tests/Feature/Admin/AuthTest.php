<?php

use App\Models\User;

it('shows the login form to guests', function () {
    $this->get(route('admin.login'))
        ->assertOk()
        ->assertSee('Sign in to Blue Control', false)
        ->assertSee('name="email"', false);
});

it('redirects guests away from the protected dashboard', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('admin.login'));
});

it('logs an active user in with the admin guard', function () {
    $user = User::factory()->create([
        'email' => 'owner@blue.test',
        'password' => bcrypt('a-secure-password-123'),
        'is_active' => true,
    ]);

    $this->post(route('admin.login.submit'), [
        'email' => 'owner@blue.test',
        'password' => 'a-secure-password-123',
    ])->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($user, 'admin');
});

it('rejects invalid credentials', function () {
    User::factory()->create(['email' => 'owner@blue.test']);

    $this->post(route('admin.login.submit'), [
        'email' => 'owner@blue.test',
        'password' => 'wrong-password-1234',
    ])->assertSessionHasErrors('email');

    $this->assertGuest('admin');
});

it('does not allow inactive users to sign in', function () {
    User::factory()->create([
        'email' => 'inactive@blue.test',
        'password' => bcrypt('a-secure-password-123'),
        'is_active' => false,
    ]);

    $this->post(route('admin.login.submit'), [
        'email' => 'inactive@blue.test',
        'password' => 'a-secure-password-123',
    ])->assertSessionHasErrors('email');

    $this->assertGuest('admin');
});

it('logs out and invalidates the session', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'admin')
        ->post(route('admin.logout'))
        ->assertRedirect(route('admin.login'));

    $this->assertGuest('admin');
});

it('allows an authenticated admin to open the dashboard', function () {
    $user = User::factory()->create(['name' => 'Owner Person']);

    $this->actingAs($user, 'admin')
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Owner Person', false)
        ->assertSee('noindex,nofollow', false);
});
