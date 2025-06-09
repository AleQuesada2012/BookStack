<?php

namespace Tests\Functional;

use BookStack\Users\Models\Role;
use BookStack\Users\Models\User;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\Group;

#[Group('coverage:functional')]
class RoleTest extends TestCase {

    public function test_get_specified_role_by_display_name() {
        $role = Role::factory()->create(['display_name' => 'rolGoood']);
        $foundRole = Role::getRole('rolGoood');
        $this->assertTrue($foundRole->id == $role->id);

    }

    public function test_get_specified_role_by_system_name() {
        $role = Role::factory()->create(['system_name' => 'rolon']);
        $foundRole = Role::getSystemRole('rolon');
        $this->assertTrue($foundRole->id == $role->id);

    }

    public function test_if_role_has_specific_permission() {

        $role = Role::query()->where('system_name', 'admin')->first();
        $this->assertTrue($role->hasPermission('settings-manage'));

    }

    public function test_if_role_has_not_specific_permission() {

        $role = Role::factory()->create(['system_name' => 'viewer']);
        $this->assertFalse($role->hasPermission('settings-manage'));

    }

    public function test_new_roles_dont_have_permissions() {
        $role = Role::factory()->create(['system_name' => 'new-role']);
        $this->assertTrue($role->permissions->isEmpty());
    }

    public function test_role_can_be_assigned_to_user() {
        $role = Role::factory()->create(['system_name' => 'new-role']);
        $user = User::factory()->create();
        $user->roles()->attach($role);
        $this->assertTrue($user->roles->contains($role));
    }

    public function test_role_can_be_removed_from_user() {
        $role = Role::factory()->create(['system_name' => 'new-role']);
        $user = User::factory()->create();
        $user->roles()->attach($role);
        $user->roles()->detach($role);
        $this->assertFalse($user->roles->contains($role));
    }

    public function test_role_can_be_updated() {
        $role = Role::factory()->create(['system_name' => 'new-role']);
        $role->display_name = 'Updated Role';
        $role->save();
        $this->assertEquals('Updated Role', $role->display_name);
    }

    public function test_role_can_be_deleted() {
        $role = Role::factory()->create(['system_name' => 'new-role']);
        $roleId = $role->id;
        $role->delete();
        $this->assertDatabaseMissing('roles', ['id' => $roleId]);
    }

    public function test_user_can_have_multiple_roles() {
        $role1 = Role::factory()->create(['system_name' => 'role-one']);
        $role2 = Role::factory()->create(['system_name' => 'role-two']);
        $user = User::factory()->create();
        $user->roles()->attach([$role1->id, $role2->id]);
        $this->assertTrue($user->roles->contains($role1));
        $this->assertTrue($user->roles->contains($role2));
    }
}

