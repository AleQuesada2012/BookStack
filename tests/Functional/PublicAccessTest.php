<?php

namespace Tests\Feature;

use Tests\TestCase;
use BookStack\Settings\SettingService;

class PublicAccessTest extends TestCase
{
    public function test_root_redirects_to_login_when_app_is_not_public()
    {
        // Set the application to private
        app(SettingService::class)->put('app-public', 'false');

        // Attempt to access the root URL
        $response = $this->get('/');

        // Assert that the response redirects to the login page
        $response->assertRedirect('/login');
    }

    public function test_root_accessible_when_app_is_public()
    {
        // Set the application to public
        app(SettingService::class)->put('app-public', 'true');

        // Access the root URL
        $response = $this->get('/');

        // Assert that the response is successful
        $response->assertStatus(200); //TODO: ver si falla cuando está en falso
    }
}
