<?php

namespace Tests\Functional;


use BookStack\Entities\Models\Book;
use BookStack\Users\Models\User;
use BookStack\Users\Models\Role;
use BookStack\Sorting\SortRule;
use Tests\Api\TestsApi;
use Tests\TestCase;


class SortRulesTest extends TestCase {

    use TestsAPi;

    public function test_order_by_alphabet_name_asc() {

        $book = Book::factory()->create();
        $rule = SortRule::factory()->create(['sequence' => 'name_asc']);
        $book->sort_rule_id = $rule->id;
        $book->save();
        $this->permissions->regenerateForEntity($book);

        $namesToAdd = ["Arrastrar", "Beso", "Correr", "Deseo", "Encajar", "Feo", "Gusto", "Hector","Iniesta"];

        $user = User::factory()->create();
        $role = Role::query()->where('system_name', 'admin')->first();
        $user->roles()->attach($role);
        
        $this->actingAs($user);

        $reverseNamesToAdd = array_reverse($namesToAdd);
        foreach ($reverseNamesToAdd as $name) {
            $this->post("/api/pages", [
                'book_id' => $book->id,
                'name' => $name,
                'markdown' => 'Hello'
            ]);

        }

        foreach ($namesToAdd as $index => $name) {
            $this->assertDatabaseHas('pages', [
                'book_id' => $book->id,
                'name' => $name,
                'priority' => $index + 1,
            ]);
        }
    }

    public function test_order_by_number_asc() {
        $book = Book::factory()->create();
        $rule = SortRule::factory()->create(['sequence' => 'name_numeric_asc']);
        $book->sort_rule_id = $rule->id;
        $book->save();

        $this->permissions->regenerateForEntity($book);

        $namesToAdd = ["1 - Ajo", "2.0 - Barrio", "2.5 - Caos", "10 - Dedos", "20 - Zeta"];

        $user = User::factory()->create();
        $role = Role::query()->where('system_name', 'admin')->first();
        $user->roles()->attach($role);
        
        $this->actingAs($user);

        $reverseNamesToAdd = array_reverse($namesToAdd);
        foreach ($reverseNamesToAdd as $name) {
            $this->post("/api/pages", [
                'book_id' => $book->id,
                'name' => $name,
                'markdown' => 'Hello'
            ]);
        }

        foreach ($namesToAdd as $index => $name) {
            $this->assertDatabaseHas('pages', [
                'book_id' => $book->id,
                'name' => $name,
                'priority' => $index + 1,
            ]);
        }
    }

}