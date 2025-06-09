<?php

namespace Tests\Feature;

use Tests\TestCase;
use BookStack\Settings\SettingService;

use PHPUnit\Framework\Attributes\Group;

#[Group('coverage:functional')]
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

        app(SettingService::class)->put('app-public', 'true');

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_root_not_accessible_when_app_is_private()
    {
        app(SettingService::class)->put('app-public', 'false');

        $response = $this->get('/');

        $response->assertStatus(302); // Expecting a redirect to login
    }
}
