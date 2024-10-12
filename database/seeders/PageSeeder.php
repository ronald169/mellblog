<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['slug' => 'terms', 'title' => 'Terms'],
            ['slug' => 'privacy-policy', 'title' => 'Privacy Policy'],
        ];

        foreach ($items as $item) {
            Page::factory()->create([
                'title'     => $item['title'],
                'seo_title' => 'Page ' . $item['title'],
                'slug'      => $item['slug'],
                'active'    => true,
            ]);
        }
    }
}
