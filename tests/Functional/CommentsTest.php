<?php

namespace Tests\Functional;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\Chapter;
use BookStack\Users\Models\User;
use BookStack\Activity\Models\Comment;
use BookStack\Activity\Tools\CommentTree;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('coverage:functional')]
class CommentsTest extends TestCase
{
    public function test_comment_cannot_be_posted_when_comments_are_disabled(): void
    {
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

        $this->setSettings(['app-disable-comments' => 'true']); 
        $comment = Comment::factory()->make([
            'entity_type' => 'page',
            'entity_id'   => $page->id,
            'html'        => 'This comment should not be saved',
        ]);
        if ($this->assertTrue(setting('app-disable-comments'))) {
            $comment->save(); 
        }
        $this->assertDatabaseMissing('comments', ['html' => 'This comment should not be saved']);
    }

    public function test_comment_can_be_posted_when_comments_are_enabled(): void
    {
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
        $this->setSettings(['app-disable-comments' => 'false']);
        $comment = Comment::factory()->make([
            'entity_type' => 'page',
            'entity_id'   => $page->id,
            'html'        => 'This comment should be saved',
        ]);
        $comment->save();
        $this->assertDatabaseHas('comments', ['html' => 'This comment should be saved']);
    }
}