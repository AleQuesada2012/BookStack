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


    public function test_register_with_invalid_password() {
        setting()->put('registration-enabled', true);
        config(['auth.method' => 'standard']);

        $userData = [
            'name' => "Joshua",
            'email' => "matazanos@gmail.com",
            'password' => "123",
            'password-confirm' => "123"
        ];

        $response = $this->post('/register', $userData);

        $response->assertRedirect('/');
        $this->assertDatabaseMissing('users', ['email' => 'matazanos@gmail.com']);
        $this->assertGuest();
    }


    public function test_register_with_invalid_email() {
        setting()->put('registration-enabled', true);
        config(['auth.method' => 'standard']);

        $userData = [
            'name' => "Memin",
            'email' => "Hola Mundo",
            'password' => "ReinhardVanAstrea3",
            'password-confirm' => "ReinhardVanAstrea3"
        ];

        $response = $this->post('/register', $userData);

        $response->assertRedirect('/');
        $this->assertDatabaseMissing('users', ['email' => 'Hola Mundo']);
        $this->assertGuest();
    }


}