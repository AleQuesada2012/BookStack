<?php

namespace Tests\Functional;

use BookStack\Users\Models\Role;
use BookStack\Users\Models\User;
use Tests\TestCase;


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

}

