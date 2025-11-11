<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Luis Henrique',
            'email' => 'junimhs10@gmail.com',
            'password' => bcrypt('junior'),
            'phone' => '62982296415'
        ]);

        $categoriesDefault = ['Alimentação', 'Transporte', 'Lazer', 'Educação', 'Saúde', 'Supérfluo', 'Salário', 'Freelancer', 'Investimentos'];

        foreach ($categoriesDefault as $category) {
            Category::create([
                'name' => $category,
                'is_default' => true
            ]);
        }
    }
}
