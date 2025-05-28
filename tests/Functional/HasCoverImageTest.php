<?php

namespace Tests\Functional;

use Tests\TestCase;
use BookStack\Entities\Models\Book;
use BookStack\Uploads\Image;

class HasCoverImageTest extends TestCase
{
    public function test_entity_has_cover_image()
    {
        $book = Book::factory()->create();
        $image = Image::factory()->create();

        $book->cover()->associate($image);
        $book->save();
        $book->refresh();

        $this->assertTrue($book->cover()->exists());
        $this->assertEquals($image->id, $book->cover->id);
        $this->assertEquals('cover_book', $book->coverImageTypeKey()); 
    }

    public function test_cover_image_type_key_correct()
    {
        $book = Book::factory()->create();
        $this->assertEquals('cover_book', $book->coverImageTypeKey()); 
    }
}