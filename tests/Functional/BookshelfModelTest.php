<?php

namespace Tests\Functional;

use Tests\TestCase;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Book;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Mockery;
use Illuminate\Support\Collection;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

use PHPUnit\Framework\Attributes\Group;

#[Group('coverage:functional')]
class BookshelfModelTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_url_generation()
    {
        $shelf = Mockery::mock(Bookshelf::class)->makePartial();
        $shelf->slug = 'test-shelf';
        
        $this->assertEquals(url('/shelves/test-shelf'), $shelf->getUrl());
        $this->assertEquals(url('/shelves/test-shelf/custom-path'), $shelf->getUrl('custom-path'));
    }

    public function test_books_relationship()
    {
        $shelf = Mockery::mock(Bookshelf::class)->makePartial();
        
        $mockRelation = Mockery::mock(BelongsToMany::class);
        $mockRelation->shouldReceive('withPivot')->with('order')->andReturnSelf();
        $mockRelation->shouldReceive('orderBy')->with('order', 'asc')->andReturnSelf();
        
        $shelf->shouldReceive('belongsToMany')
            ->with(Book::class, 'bookshelves_books', 'bookshelf_id', 'book_id')
            ->andReturn($mockRelation);
        
        $this->assertInstanceOf(BelongsToMany::class, $shelf->books());
    }

    public function test_visible_books_scope()
    {
        $shelf = Mockery::mock(Bookshelf::class)->makePartial();
        
        $baseRelation = Mockery::mock(BelongsToMany::class);
        $baseRelation->shouldReceive('scopes')->with('visible')->andReturnSelf();
        
        $shelf->shouldReceive('books')
            ->andReturn($baseRelation);
        
        $this->assertInstanceOf(BelongsToMany::class, $shelf->visibleBooks());
    }

    public function test_cover_relationship()
    {
        $shelf = Mockery::mock(Bookshelf::class)->makePartial();
        
        $mockRelation = Mockery::mock(BelongsTo::class);
        $shelf->shouldReceive('belongsTo')
            ->with('BookStack\Uploads\Image', 'image_id')
            ->andReturn($mockRelation);
        
        $this->assertInstanceOf(BelongsTo::class, $shelf->cover());
    }

    public function test_cover_image_type_key()
    {
        $shelf = new Bookshelf();
        $this->assertEquals('cover_bookshelf', $shelf->coverImageTypeKey());
    }

    public function test_get_book_cover_with_image()
    {
        $shelf = Mockery::mock(Bookshelf::class)->makePartial();
        $shelf->image_id = 1;
        
        $mockCover = Mockery::mock();
        $mockCover->shouldReceive('getThumb')
            ->with(440, 250, false)
            ->andReturn('http://example.com/thumbs-440x250/image.jpg');
        
        $shelf->shouldReceive('getAttribute')
            ->with('cover')
            ->andReturn($mockCover);
        
        $coverUrl = $shelf->getBookCover(440, 250);
        $this->assertStringContainsString('thumbs-440x250', $coverUrl);
    }

    public function test_get_book_cover_without_image()
    {
        $shelf = Mockery::mock(Bookshelf::class)->makePartial();
        $shelf->image_id = null;
        
        $shelf->shouldReceive('getAttribute')
            ->with('cover')
            ->andReturn(null);
        
        $coverUrl = $shelf->getBookCover();
        $this->assertEquals('data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==', $coverUrl);
    }

    public function test_contains_method()
    {
        $shelf = Mockery::mock(Bookshelf::class)->makePartial();
        $book = Mockery::mock(Book::class);
        $book->shouldReceive('getAttribute')->with('id')->andReturn(1);
        
        $mockRelation = Mockery::mock(BelongsToMany::class);
        $mockRelation->shouldReceive('where')
            ->with('id', '=', 1)
            ->andReturnSelf();
        $mockRelation->shouldReceive('count')
            ->andReturn(1);
        
        $shelf->shouldReceive('books')
            ->andReturn($mockRelation);
        
        $this->assertTrue($shelf->contains($book));
    }

    public function test_append_book_new_book()
    {
        $shelf = Mockery::mock(Bookshelf::class)->makePartial();
        $book = Mockery::mock(Book::class);
        $book->shouldReceive('getAttribute')->with('id')->andReturn(1);
        
        $shelf->shouldReceive('contains')
            ->with($book)
            ->andReturn(false);
        
        $mockRelation = Mockery::mock(BelongsToMany::class);
        $mockRelation->shouldReceive('max')
            ->with('order')
            ->andReturn(5);
        $mockRelation->shouldReceive('attach')
            ->with(1, ['order' => 6])
            ->once();
        
        $shelf->shouldReceive('books')
            ->andReturn($mockRelation);
        
        $shelf->appendBook($book);
    }

    public function test_append_book_existing_book()
    {
        $shelf = Mockery::mock(Bookshelf::class)->makePartial();
        $book = Mockery::mock(Book::class);
        
        $shelf->shouldReceive('contains')
            ->with($book)
            ->andReturn(true);
        
        $shelf->shouldReceive('books')
            ->never();
        
        $shelf->appendBook($book);
    }

    public function test_fillable_properties()
    {
        $shelf = new Bookshelf();
        $this->assertEquals(['name', 'description', 'image_id'], $shelf->getFillable());
    }

    public function test_hidden_properties()
    {
        $shelf = new Bookshelf();
        $this->assertEquals(['image_id', 'deleted_at', 'description_html'], $shelf->getHidden());
    }

    public function test_search_factor()
    {
        $shelf = new Bookshelf();
        $this->assertEquals(1.2, $shelf->searchFactor);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}