<?php
// tests/Functional/ProfilePicTest.php

namespace Tests\Functional;

use Tests\TestCase;
use BookStack\Users\Models\User;
use BookStack\Uploads\Image;

use PHPUnit\Framework\Attributes\Group;

#[Group('coverage:functional')]
class ProfilePicTest extends TestCase
{

    public function test_user_can_have_an_avatar_image()
    {
        // Create a user and an image record
        $user  = User::factory()->create();
        $image = Image::factory()->create();

        // Associate via image_id
        $user->image_id = $image->id;
        $user->save();
        $user->refresh();

        // Relation exists and points to the correct image
        $this->assertTrue($user->avatar()->exists());
        $this->assertEquals($image->id, $user->avatar->id);
    }


    public function test_user_without_avatar_gets_default_avatar_url()
    {
        $user = User::factory()->create();

        $defaultUrl = url('/user_avatar.png');
        $this->assertEquals($defaultUrl, $user->getAvatar(80));
    }

    public function test_user_can_reset_avatar_to_default()
    {
        $user  = User::factory()->create();
        $image = Image::factory()->create();


        $user->image_id = $image->id;
        $user->save();
        $user->refresh();

        // simulate reset to default avatar
        $user->image_id = 0;
        $user->save();
        $user->refresh();


        $this->assertFalse($user->avatar()->exists());


        $defaultUrl = url('/user_avatar.png');
        $this->assertEquals($defaultUrl, $user->getAvatar(50));
    }
}
