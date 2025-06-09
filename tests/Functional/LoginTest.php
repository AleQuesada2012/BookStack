<?php

namespace Tests\Functional;

use Tests\TestCase;
use \BookStack\Users\Models\User;


use PHPUnit\Framework\Attributes\Group;

#[Group('coverage:functional')]
class LoginTest extends TestCase {

    public function test_login_fails() {
        $response = $this->post('/login', [
            'email'    => 'EmailRandom@AbuelaSinValor.com',
            'password' => 'ContraProgra',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }


    public function test_login_successfully () { 

        $user = User::factory()->create([ 
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);

    }
}
