<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultCategories = [
            // Receitas
            'Salário',
            'Freelance',
            'Investimentos',
            'Outros Recebimentos',

            // Despesas Essenciais
            'Alimentação',
            'Moradia',
            'Transporte',
            'Saúde',
            'Educação',

            // Despesas Variáveis
            'Lazer',
            'Compras',
            'Restaurantes',
            'Viagens',
            'Assinaturas',

            // Despesas Fixas
            'Contas e Serviços',
            'Internet e Telefone',
            'Academia',

            // Outros
            'Impostos',
            'Seguros',
            'Doações',
            'Pets',
            'Beleza e Cuidados',
            'Outros',
        ];

        foreach ($defaultCategories as $categoryName) {
            Category::firstOrCreate(
                [
                    'name' => $categoryName,
                    'is_default' => true,
                ],
                [
                    'uuid' => Str::uuid(),
                    'user_id' => null,
                    'status' => true,
                ]
            );
        }
    }
}
