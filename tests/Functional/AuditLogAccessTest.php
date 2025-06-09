<?php

namespace Tests\Functional;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use BookStack\Users\Models\User;
use BookStack\Users\Models\Role;

use PHPUnit\Framework\Attributes\Group;

#[Group('coverage:functional')]
class AuditLogAccessTest extends TestCase
{



    public function test_admin_user_can_access_audit_log_route()
    {
        $user = User::factory()->create();
        $role = Role::query()->where('system_name', 'admin')->first();
        $user->roles()->attach($role);
        $this->actingAs($user);

        $response = $this->get('/settings/audit');

        $response->assertStatus(200);
    }


    public function test_editor_user_cannot_access_audit_log_route()
    {
        $editor = User::factory()->create();
        $role   = Role::query()->where('system_name', 'editor')->first();
        $editor->roles()->attach($role);
        $this->actingAs($editor);
    
        $this->withoutExceptionHandling();
    
        $this->expectException(\BookStack\Exceptions\NotifyException::class);
        $this->expectExceptionMessage('You do not have permission to access the requested page.');
        $this->get('/settings/audit');
    }
}
