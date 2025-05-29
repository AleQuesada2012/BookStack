<?php

namespace Tests\Functional;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Book;
use BookStack\Users\Models\User;
use BookStack\Uploads\Attachment;
use BookStack\Entities\Models\PageRevision;
use Tests\TestCase;

class PageModelTest extends TestCase {

    public function test_page_has_any_chapter() {
        $user = User::factory()->create();
        $book = Book::factory()->create(['created_by' => $user->id]);

        $chapter = Chapter::factory()->create([
            'book_id' => $book->id,
            'created_by' => $user->id,
        ]);

        $page = Page::factory()->create([
            'book_id' => $book->id,
            'chapter_id' => $chapter->id,
            'created_by' => $user->id,
        ]);

        $this->assertTrue($page->hasChapter());
    }

    public function test_page_has_not_any_chapter() {
        $user = User::factory()->create();
        $book = Book::factory()->create(['created_by' => $user->id]);

        $chapter = Chapter::factory()->create([
            'book_id' => $book->id,
            'created_by' => $user->id,
        ]);

        $page = Page::factory()->create([
            'book_id' => $book->id,
            'created_by' => $user->id,
        ]);

        $this->assertFalse($page->hasChapter());
    }

    public function test_page_has_specific_chapter() {

        $user = User::factory()->create();
        $book = Book::factory()->create(['created_by' => $user->id]);

        $chapter = Chapter::factory()->create([
            'book_id' => $book->id,
            'created_by' => $user->id,
        ]);

        $page = Page::factory()->create([
            'book_id' => $book->id,
            'chapter_id' => $chapter->id,
            'created_by' => $user->id,
        ]);

        $this->assertTrue($page->chapter->id == $chapter->id);
    }


    public function test_page_has_not_specific_chapter() {

        $user = User::factory()->create();
        $book = Book::factory()->create(['created_by' => $user->id]);

        $chapter1 = Chapter::factory()->create([
            'book_id' => $book->id,
            'created_by' => $user->id,
        ]);

        $chapter2 = Chapter::factory()->create([
            'book_id' => $book->id,
            'created_by' => $user->id,
        ]);

        $page = Page::factory()->create([
            'book_id' => $book->id,
            'chapter_id' => $chapter1->id,
            'created_by' => $user->id,
        ]);

        $this->assertFalse($page->chapter->id == $chapter2->id);
    }


    public function test_page_has_specific_attachment() {

        $user = User::factory()->create();
        $this->actingAs($user);


        $page = Page::factory()->create(['created_by' => $user->id,'updated_by' => $user->id]);

        
        $attachment = Attachment::factory()->create(['uploaded_to' => $page->id, 'order' => 2]);
        $attachments = $page->attachments;


        $this->assertEquals($attachment->id, $attachments->first()->id);
    }


    public function test_page_has_not_specific_attachment() {

        $user = User::factory()->create();
        $this->actingAs($user);


        $page = Page::factory()->create(['created_by' => $user->id,'updated_by' => $user->id]);

        
        $attachment1 = Attachment::factory()->create(['uploaded_to' => $page->id, 'order' => 2]);
        $attachment2 = Attachment::factory()->create(['order' => 3]);
        $attachments = $page->attachments;


        $this->assertFalse($attachment2->id == $attachments->first()->id);
    }


    public function test_get_page_url() {
        $book = Book::factory()->create(['slug' => 'mi-libro']);

        $page = Page::factory()->create(['book_id' => $book->id, 'slug' => 'pagina-1']);

        $url = $page->getUrl();

        $expected = url('/books/mi-libro/page/pagina-1');

        $this->assertEquals($expected, $url);
    }

}