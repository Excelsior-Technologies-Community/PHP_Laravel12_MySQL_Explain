# PHP_Laravel12_MySQL_Explain

## Introduction

PHP_Laravel12_MySQL_Explain is a Laravel 12-based web application designed to analyze and understand MySQL query execution using the EXPLAIN statement.

This project helps developers visualize how MySQL processes queries and identify performance bottlenecks. It demonstrates how query optimization and proper indexing can significantly improve database performance.

The system compares query execution plans before and after optimization, making it easier to understand the impact of indexes and efficient query writing.

---

## Project Introduction

In modern web applications, database performance plays a crucial role in overall system efficiency. Poorly written queries can lead to slow response times and high server load.

This project demonstrates how MySQL evaluates queries using the EXPLAIN command and how developers can optimize queries using indexing techniques.

The application includes:

- Execution of raw SQL queries
- Analysis using EXPLAIN
- Visualization of query execution plans
- Before vs After optimization comparison
- Index implementation for performance improvement

By using this project, developers can clearly understand:

- How MySQL selects indexes
- How many rows are scanned
- Whether a query is optimized or not
- How to improve query performance using best practices

---

## Features

- Laravel 12 setup with MySQL
- Query execution using Eloquent & Query Builder
- MySQL EXPLAIN analysis
- Query optimization techniques
- Indexing implementation
- Performance comparison (before vs after optimization)
- Clean Blade UI to display query plans

---

## Tech Stack

- Laravel 12
- PHP 8.2+
- MySQL
- Composer
- Blade Templates

---

## Step 1: Create Laravel 12 Project

```bash
composer create-project laravel/laravel PHP_Laravel12_MySQL_Explain
cd PHP_Laravel12_MySQL_Explain
```

---

## Step 2: Configure Database

Update .env file:

```.env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mysql_explain_db
DB_USERNAME=root
DB_PASSWORD=
```

Run Migration Command:

```bash
php artisan migrate
```

---

## Step 3: Create Model & Migration

Run:

```bash
php artisan make:model Product -m
```

---

## Step 4: Migration File

File: `database/migrations/xxxx_create_products_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```
Run migration:

```bash
php artisan migrate
```

---

## Step 5: Model

File: `app/Models/Product.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
    ];
}
```

---

## Step 6: Add Sample Data

```bash
php artisan make:seeder ProductSeeder
```

### Seeder Code

File: `database/seeders/ProductSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 1000; $i++) {
            Product::create([
                'name' => 'Product ' . $i,
                'description' => 'Description for product ' . $i,
                'price' => rand(100, 1000),
            ]);
        }
    }
}
```
---

Run seeder:

```bash
php artisan db:seed --class=ProductSeeder
```
---

## Step 7: Create Controller

Run:

```bash
php artisan make:controller QueryExplainController
```
---

## Step 8: Basic Explain Query

File: `app/Http/Controllers/QueryExplainController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class QueryExplainController extends Controller
{
    public function index()
    {
        // Before (slow query)
        $queryBefore = "SELECT * FROM products WHERE price > 500";
        $explainBefore = DB::select("EXPLAIN " . $queryBefore);

        // After (optimized query)
        $queryAfter = "SELECT * FROM products WHERE price > 900";
        $explainAfter = DB::select("EXPLAIN " . $queryAfter);

        return view('explain', compact(
            'queryBefore',
            'explainBefore',
            'queryAfter',
            'explainAfter'
        ));
    }
}
```

---

## Step 9: Add Index (Optimization)

Create Migration

```bash
php artisan make:migration add_price_index_to_products_table
```

Migration Code

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index('price'); // adding index
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['price']);
        });
    }
};
```

Run Migration

```bash
php artisan migrate
```

---

## Step 10: Add Route

File: `routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QueryExplainController;

Route::get('/explain', [QueryExplainController::class, 'index']);

Route::get('/', function () {
    return view('welcome');
});
```

---

## Step 11: Blade View

Create: 

`resources/views/explain.blade.php`

```blade
<!DOCTYPE html>
<html>
<head>
    <title>MySQL Query Analyzer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: #f5f7fb;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        h2 {
            margin-top: 0;
            font-size: 20px;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            color: #fff;
            margin-left: 10px;
        }

        .badge-red {
            background: #e74c3c;
        }

        .badge-green {
            background: #2ecc71;
        }

        pre {
            background: #1e1e1e;
            color: #00ff9c;
            padding: 12px;
            border-radius: 6px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #e0e0e0;
            text-align: center;
            font-size: 14px;
        }

        th {
            background: #2c3e50;
            color: white;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        .key-empty {
            color: #e74c3c;
            font-weight: bold;
        }

        .key-used {
            color: #2ecc71;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">

    <!-- BEFORE -->
    <div class="card">
        <h2>
            🔴 Before Optimization
            <span class="badge badge-red">BAD QUERY</span>
        </h2>

        <pre>{{ $queryBefore }}</pre>

        <table>
            <tr>
                <th>ID</th>
                <th>Select Type</th>
                <th>Table</th>
                <th>Type</th>
                <th>Possible Keys</th>
                <th>Key</th>
                <th>Rows</th>
                <th>Extra</th>
            </tr>

            @foreach($explainBefore as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->select_type }}</td>
                    <td>{{ $row->table }}</td>
                    <td>{{ $row->type }}</td>
                    <td>{{ $row->possible_keys }}</td>
                    <td class="{{ $row->key ? 'key-used' : 'key-empty' }}">
                        {{ $row->key ?: 'NULL' }}
                    </td>
                    <td>{{ $row->rows }}</td>
                    <td>{{ $row->Extra }}</td>
                </tr>
            @endforeach
        </table>
    </div>

    <!-- AFTER -->
    <div class="card">
        <h2>
            🟢 After Optimization
            <span class="badge badge-green">OPTIMIZED</span>
        </h2>

        <pre>{{ $queryAfter }}</pre>

        <table>
            <tr>
                <th>ID</th>
                <th>Select Type</th>
                <th>Table</th>
                <th>Type</th>
                <th>Possible Keys</th>
                <th>Key</th>
                <th>Rows</th>
                <th>Extra</th>
            </tr>

            @foreach($explainAfter as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->select_type }}</td>
                    <td>{{ $row->table }}</td>
                    <td>{{ $row->type }}</td>
                    <td>{{ $row->possible_keys }}</td>
                    <td class="{{ $row->key ? 'key-used' : 'key-empty' }}">
                        {{ $row->key ?: 'NULL' }}
                    </td>
                    <td>{{ $row->rows }}</td>
                    <td>{{ $row->Extra }}</td>
                </tr>
            @endforeach
        </table>
    </div>

</div>

</body>
</html>
```

---

## Step 12: Run Project

Run:

```bash
php artisan serve
```

Open browser:

```bash
http://127.0.0.1:8000/explain
```

---

## Output

<img src="screenshots/Screenshot 2026-03-25 105941.png" width="1000">

---

## Project Structure

```
PHP_Laravel12_MySQL_Explain/
│
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── QueryExplainController.php
│   │
│   └── Models/
│       └── Product.php   
│
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   │   └── xxxx_create_products_table.php
│   │
│   └── seeders/
│       └── ProductSeeder.php
│
├── public/
├── resources/
│   ├── views/
│   │   └── explain.blade.php
│   │
│   └── js/
│   └── css/
│
├── routes/
│   └── web.php
│
├── storage/
├── tests/
├── vendor/
│
├── .env
├── artisan
├── composer.json
└── README.md
```

---

Your PHP_Laravel12_MySQL_Explain Project is now ready!



