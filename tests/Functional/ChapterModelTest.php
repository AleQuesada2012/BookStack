<?php

namespace Tests\Functional;

use Tests\TestCase;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Book;
use BookStack\Users\Models\User;
use BookStack\Entities\Models\Page;


class ChapterModelTest extends TestCase {

    public function test_get_associated_pages() {

        $user = User::factory()->create();
        $book = Book::factory()->create(['created_by' => $user->id]);

        $chapter = Chapter::factory()->create(['book_id' => $book->id, 'created_by' => $user->id,]);

        $page1 = Page::factory()->create(['book_id' => $book->id, 'chapter_id' => $chapter->id, 'created_by' => $user->id]);

        $page2 = Page::factory()->create(['book_id' => $book->id,'chapter_id' => $chapter->id,'created_by' => $user->id]);

        $associatedPages = $chapter->pages()->get();

        $this->assertCount(2, $associatedPages);
        $this->assertTrue($page1->id == $associatedPages->first()->id);
        $this->assertTrue($page2->id == $associatedPages->last()->id);

    }

    public function test_get_url_from_chapter() {
        $book = Book::factory()->create(['slug' => 'mi-libro']);

        $chapter = Chapter::factory()->create(['book_id' => $book->id, 'slug' => 'capitulo-1']);

        $url = $chapter->getUrl();

        $expected = url('/books/mi-libro/chapter/capitulo-1');

        $this->assertEquals($expected, $url);
    }

}