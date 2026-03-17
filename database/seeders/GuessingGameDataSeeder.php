<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GuessingGameDataSeeder extends Seeder
{
  
    private array $data = [
        'animals' => [
            'Lion'     => 'King of the jungle, known for its mane',
            'Elephant' => 'Largest land animal with a trunk',
            'Eagle'    => 'Large flying bird with excellent vision',
            'Dolphin'  => 'Intelligent marine mammal',
            'Tiger'    => 'Striped big cat known for strength and stealth',
        ],
        'countries' => [
            'Japan'     => 'Island nation in East Asia, known for technology',
            'Brazil'    => 'Largest country in South America',
            'Germany'   => 'European country known for engineering',
            'Australia' => 'Island continent in the Southern Hemisphere',
            'Canada'    => 'North American country known for maple leaves and vast forests',
        ],
        'programming_languages' => [
            'PHP'        => 'Server-side language, powers many websites',
            'Python'     => 'General-purpose language, popular in data science',
            'JavaScript' => 'Language that runs in web browsers',
            'Java'       => 'Object-oriented language, "write once, run anywhere"',
            'Ruby'       => 'Developer-friendly language famous for Ruby on Rails',
        ],
    ];

    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('hints')->truncate();
        DB::table('words')->truncate();
        DB::table('categories')->truncate();
        Schema::enableForeignKeyConstraints();

        $now = now();

        foreach ($this->data as $categoryName => $wordHints) {
            $categoryId = DB::table('categories')->insertGetId([
                'name'       => $categoryName,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($wordHints as $word => $hint) {
                $wordId = DB::table('words')->insertGetId([
                    'category_id' => $categoryId,
                    'word'        => $word,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);

                DB::table('hints')->insert([
                    'word_id'    => $wordId,
                    'hint'       => $hint,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}

