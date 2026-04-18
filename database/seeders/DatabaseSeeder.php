<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use App\Models\Fine;
use Illuminate\Database\Seeder;
use App\Models\BookReview;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear 10 usuarios
        $users = User::factory(10)->create();
        // 2. Crear 15 autores
        $authors = Author::factory(15)->create();
        // 3. Ejecutar CategorySeeder (10 categorías)
        $this->call(CategorySeeder::class);
        $categoryIds = \App\Models\Category::pluck('id');
        // 4. Crear 50 libros con categoría random
        $books = Book::factory(50)->create([
            'category_id' => fn() => $categoryIds->random(),
        ]);
        // 5. Asignar 1-3 autores a cada libro
        $books->each(function (Book $book) use ($authors) {
            $randomAuthors = $authors->random(
                rand(1, 3)
            );
            $book->authors()->attach(
                $randomAuthors->pluck('id'),
                ['role' => 'Autor']
            );
        });
        // 6. Crear miembros para los primeros 8 usuarios
        $members = $users->take(8)->map(
            fn(User $user) => Member::factory()->create([
                'user_id' => $user->id,
            ])
        );
        // 7. Crear 20 préstamos
        $memberIds = $members->pluck('id');
        $bookIds = $books->pluck('id');
        $loans = Loan::factory(20)->create([
            'book_id' => fn() => $bookIds->random(),
            'member_id' => fn() => $memberIds->random(),
            'loaned_by' => fn() => $users->random()->id,
        ]);
        // 8. Crear multas para préstamos vencidos
        $loans->where('status', 'overdue')->each(function ($loan) {
            Fine::factory()->create([
                'loan_id' => $loan->id,
            ]);
        });
        //9. creamos 30 resenas asociados con miembros y libros existentes
        $reviews = BookReview::factory(30)->create([
            'rating' => fake()->numberBetween(1, 5),
        ]);
    }
}
