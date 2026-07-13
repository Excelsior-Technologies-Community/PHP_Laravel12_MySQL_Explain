<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QueryPerformanceLog;

class QueryPerformanceLogSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'query'          => 'SELECT * FROM products WHERE price > 500',
                'execution_time' => 142.5,
                'rows_examined'  => 980,
                'key_used'       => null,
                'created_at'     => now()->subDays(6),
                'updated_at'     => now()->subDays(6),
            ],
            [
                'query'          => 'SELECT * FROM products WHERE price > 900',
                'execution_time' => 38.2,
                'rows_examined'  => 102,
                'key_used'       => 'products_price_index',
                'created_at'     => now()->subDays(6),
                'updated_at'     => now()->subDays(6),
            ],
            [
                'query'          => 'SELECT * FROM products WHERE name LIKE "%Product 5%"',
                'execution_time' => 210.8,
                'rows_examined'  => 1000,
                'key_used'       => null,
                'created_at'     => now()->subDays(5),
                'updated_at'     => now()->subDays(5),
            ],
            [
                'query'          => 'SELECT id, name, price FROM products ORDER BY price DESC LIMIT 10',
                'execution_time' => 55.3,
                'rows_examined'  => 200,
                'key_used'       => 'products_price_index',
                'created_at'     => now()->subDays(5),
                'updated_at'     => now()->subDays(5),
            ],
            [
                'query'          => 'SELECT * FROM products WHERE price BETWEEN 200 AND 800',
                'execution_time' => 88.7,
                'rows_examined'  => 600,
                'key_used'       => 'products_price_index',
                'created_at'     => now()->subDays(4),
                'updated_at'     => now()->subDays(4),
            ],
            [
                'query'          => 'SELECT COUNT(*) FROM products WHERE price > 300',
                'execution_time' => 31.1,
                'rows_examined'  => 700,
                'key_used'       => 'products_price_index',
                'created_at'     => now()->subDays(3),
                'updated_at'     => now()->subDays(3),
            ],
            [
                'query'          => 'SELECT * FROM products WHERE description LIKE "%description%"',
                'execution_time' => 320.4,
                'rows_examined'  => 1000,
                'key_used'       => null,
                'created_at'     => now()->subDays(3),
                'updated_at'     => now()->subDays(3),
            ],
            [
                'query'          => 'SELECT * FROM products WHERE price = 500',
                'execution_time' => 22.9,
                'rows_examined'  => 15,
                'key_used'       => 'products_price_index',
                'created_at'     => now()->subDays(2),
                'updated_at'     => now()->subDays(2),
            ],
            [
                'query'          => 'SELECT name, price FROM products WHERE price < 200 ORDER BY name',
                'execution_time' => 67.6,
                'rows_examined'  => 180,
                'key_used'       => 'products_price_index',
                'created_at'     => now()->subDays(1),
                'updated_at'     => now()->subDays(1),
            ],
            [
                'query'          => 'SELECT * FROM products',
                'execution_time' => 415.0,
                'rows_examined'  => 1000,
                'key_used'       => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ];

        QueryPerformanceLog::insert($data);

        $this->command->info('✅ 10 Query Performance Logs inserted successfully!');
    }
}
