<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $rows = [
            [
                'title' => '1984',
                'author' => 'George Orwell',
                'summary' => 'Roman dystopique decrivant une societe totalitaire controlee par Big Brother.',
                'isbn' => '9780451524935',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Dune',
                'author' => 'Frank Herbert',
                'summary' => 'Epopee de science-fiction centree sur la planete Arrakis et les enjeux autour de l epice.',
                'isbn' => '9780441013593',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Le Seigneur des Anneaux',
                'author' => 'J.R.R. Tolkien',
                'summary' => 'Trilogie racontant la quete pour detruire l Anneau unique et vaincre Sauron.',
                'isbn' => '9780544003415',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        Book::insert($rows);
    }
}
