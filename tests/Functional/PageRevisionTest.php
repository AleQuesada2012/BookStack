<?php

namespace Tests\Functional;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\PageRevision;
use BookStack\Users\Models\User;
use Tests\TestCase;

class PageRevisionTest extends TestCase
{
    public function test_log_descriptor_returns_expected_string()
    {
$user = \BookStack\Users\Models\User::factory()->create([
    'name' => 'Editor',
    'email' => 'editor@example.com',
    'system_name' => 'editor',
]);
$this->actingAs($user);
        $this->actingAs($user);

        // Crea una página base
        $page = Page::factory()->create();

        // Crea una revisión para esa página
        $revision = PageRevision::query()->create([
            'page_id' => $page->id,
            'name' => 'Test Revision',
            'slug' => 'test-revision',
            'book_slug' => $page->book->slug ?? 'default-book',
            'created_by' => $user->id,
            'type' => 'version',
            'summary' => 'This is a test summary',
            'markdown' => '# Heading',
            'html' => '<h1>Heading</h1>',
            'text' => 'Heading',
            'revision_number' => 1,
        ]);

        $expected = "Revision #1 (ID: {$revision->id}) for page ID {$page->id}";
        $this->assertEquals($expected, $revision->logDescriptor());
    }

    public function test_get_url_returns_expected_path()
    {
$user = \BookStack\Users\Models\User::factory()->create([
    'name' => 'Editor',
    'email' => 'editor@example.com',
    'system_name' => 'editor',
]);
$this->actingAs($user);
        $this->actingAs($user);

        // Crea una página base
        $page = Page::factory()->create();

        // Crea una revisión
        $revision = PageRevision::query()->create([
            'page_id' => $page->id,
            'name' => 'Test Revision',
            'slug' => 'test-revision',
            'book_slug' => $page->book->slug ?? 'default-book',
            'created_by' => $user->id,
            'type' => 'version',
            'summary' => 'This is a test summary',
            'markdown' => '# Heading',
            'html' => '<h1>Heading</h1>',
            'text' => 'Heading',
            'revision_number' => 1,
        ]);

        $url = $revision->getUrl();

        $this->assertStringContainsString("/revisions/{$revision->id}", $url);
        $this->assertStringContainsString((string) $page->id, $url);
    }
}
