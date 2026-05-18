<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/seeders/data/Books.json');
        $items = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        $now = now();

        $rows = array_map(function (array $item) use ($now): array {
            return [
                'title' => $item['title'],
                'author' => $item['author'],
                'summary' => $item['summary'],
                'isbn' => $item['isbn'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $items);

        Book::insert($rows);
    }
}
