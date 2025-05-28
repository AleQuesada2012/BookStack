<?php

namespace Tests\Functional;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\PageRevision;
use BookStack\Users\Models\User;
use Tests\TestCase;

class PageRevisionTest extends TestCase
{
    private function createEditorAndPage(): array
    {
        $user = User::factory()->create([
            'name' => 'Editor',
            'email' => 'editor@example.com',
            'system_name' => 'editor',
        ]);
        $this->actingAs($user);

        $book = Book::factory()->create();

        $page = Page::factory()->create([
            'book_id' => $book->id,
        ]);

        return [$user, $page];
    }

    public function test_log_descriptor_returns_expected_string()
    {
        [$user, $page] = $this->createEditorAndPage();

        // Use forceFill to assign all necessary fields
        $revision = (new PageRevision())->forceFill([
            'page_id' => $page->id,
            'name' => 'Test Revision',
            'slug' => 'test-revision',
            'book_slug' => $page->book->slug,
            'created_by' => $user->id,
            'type' => 'version',
            'summary' => 'This is a test summary',
            'markdown' => '# Heading',
            'html' => '<h1>Heading</h1>',
            'text' => 'Heading',
            'revision_number' => 1,
        ]);
        $revision->save();

        // Reload with relationship loaded
        $revision->load('page');

        $expected = "Revision #1 (ID: {$revision->id}) for page ID {$revision->page->id}";
        $this->assertEquals($expected, $revision->logDescriptor());
    }

    public function test_get_url_returns_expected_path()
    {
        [$user, $page] = $this->createEditorAndPage();

        $revision = (new PageRevision())->forceFill([
            'page_id' => $page->id,
            'name' => 'Test Revision',
            'slug' => 'test-revision',
            'book_slug' => $page->book->slug,
            'created_by' => $user->id,
            'type' => 'version',
            'summary' => 'This is a test summary',
            'markdown' => '# Heading',
            'html' => '<h1>Heading</h1>',
            'text' => 'Heading',
            'revision_number' => 1,
        ]);
        $revision->save();

        // Make sure related models are available
        $revision->load('page.book');

        $url = $revision->getUrl();

        $this->assertStringContainsString("/revisions/{$revision->id}", $url);
$this->assertStringContainsString($page->slug, $url);
    }
}
