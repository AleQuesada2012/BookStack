<?php

namespace Tests\Functional;
use Tests\TestCase;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Users\Models\User;

use PHPUnit\Framework\Attributes\Group;

#[Group('coverage:functional')]
class SoftDeleteTest extends TestCase { 

    public function test_soft_delete_book() {

        $user = User::factory()->create();
        $this->actingAs($user);

        $book = Book::factory()->create(['owned_by' => $user->id, 'created_by' => $user->id, 'updated_by' => $user->id]);
        $this->assertDatabaseHas('books', ['id' => $book->id]);
        $book->delete();
        $this->assertSoftDeleted('books', ['id' => $book->id]);
    }


    public function test_soft_delete_page() { 

        $user = User::factory()->create();
        $this->actingAs($user);

        $book = Book::factory()->create(['owned_by' => $user->id, 'created_by' => $user->id, 'updated_by' => $user->id]);
        $page = Page::factory()->create(['book_id' => $book->id, 'created_by' => $user->id, 'updated_by' => $user->id]);

        $this->assertDatabaseHas('pages', ['id' => $page->id]);
        $page->delete();
        $this->assertSoftDeleted('pages', ['id' => $page->id]);

    }


    public function test_soft_delete_chapter() {
        $user = User::factory()->create();
        $this->actingAs($user);

        $book = Book::factory()->create(['owned_by' => $user->id, 'created_by' => $user->id, 'updated_by' => $user->id]);
        $chapter = Chapter::factory()->create(['book_id' => $book->id, 'created_by' => $user->id, 'updated_by' => $user->id]);

        $this->assertDatabaseHas('chapters', ['id' => $chapter->id]);
        $chapter->delete();
        $this->assertSoftDeleted('chapters', ['id' => $chapter->id]);
    }


    public function test_soft_delete_bookshelf() {
        $user = User::factory()->create();
        $this->actingAs($user);

        $shelf = Bookshelf::factory()->create(['owned_by' => $user->id, 'created_by' => $user->id, 'updated_by' => $user->id]);

        $this->assertDatabaseHas('bookshelves', ['id' => $shelf->id]);
        $shelf->delete();
        $this->assertSoftDeleted('bookshelves', ['id' => $shelf->id]);
    }

}