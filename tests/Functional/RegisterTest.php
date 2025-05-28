<?php

namespace Tests\Feature;

use Tests\TestCase;

class RegisterTest extends TestCase {

    public function test_register_with_registration_enabled() {

        setting()->put('registration-enabled', true);
        config(['auth.method' => 'standard']);

        $userData = [
            'name' => "Joshua",
            'email' => "jos_jimenez@gmail.com",
            'password' => "ReinhardVanAstrea1",
            'password-confirm' => "ReinhardVanAstrea1"
        ];

        $response = $this->post('/register', $userData);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', ['email' => 'jos_jimenez@gmail.com']);
        $this->assertAuthenticated();


    }


    public function test_register_with_registration_disabled() {

        setting()->put('registration-enabled', false);
        config(['auth.method' => 'standard']);

        $userData = [
            'name' => "Victor",
            'email' => "garbanzo.eskeler@gmail.com",
            'password' => "ReinhardVanAstrea2",
            'password-confirm' => "ReinhardVanAstrea2"
        ];

        $response = $this->post('/register', $userData);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('users', ['email' => 'garbanzo.eskeler@gmail.com']);
        $this->assertGuest();


    }


}