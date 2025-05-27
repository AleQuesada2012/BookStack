<?php

namespace Tests\Functional;

use Tests\TestCase;
// use Illuminate\Foundation\Testing\RefreshDatabase; 

class LoginTest extends TestCase {

    // use RefreshDatabase;

    public function test_login_fails() {
        $response = $this->post('/login', [
            'email'    => 'EmailRandom@AbuelaSinValor.com',
            'password' => 'ContraProgra',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }


    // public function test_login_successfully () {

    //     // Crear un usuario de prueba en la base de datos
    //     $user = \BookStack\Entities\Models\User::factory()->create([
    //         'password' => bcrypt('password123'),
    //     ]);

    //     // Intentar iniciar sesión
    //     $response = $this->post('/login', [
    //         'email'    => $user->email,
    //         'password' => 'password123',
    //     ]);

    //     // Verificar que fue redirigido correctamente (por defecto a /)
    //     $response->assertRedirect('/');
    //     $this->assertAuthenticatedAs($user);

    // }
}

