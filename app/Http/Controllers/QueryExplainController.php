<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\QueryPerformanceLog;
use Illuminate\Support\Facades\DB;

class QueryExplainController extends Controller
{
    // ─── EXISTING: Product Dashboard ────────────────────────────────────────────

    public function index(Request $request)
    {
        $search = $request->search;
        $productsQuery = Product::query();

        if ($search) {
            $productsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
                if (is_numeric($search)) {
                    $query->orWhere('price', $search);
                }
            });
        }

        $products = $productsQuery->orderBy('id', 'asc')->paginate(3);
        $totalProducts = Product::count();
        $highPriceProducts = Product::where('price', '>', 500)->count();

        return view('explain', compact('products', 'search', 'totalProducts', 'highPriceProducts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'price'       => 'required|numeric|min:0',
        ]);

        Product::create([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
        ]);

        return redirect('/explain')->with('success', 'Product added successfully!');
    }

    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Product deleted successfully!');
    }

    // ─── FEATURE 1: Visual Query Execution Plan ──────────────────────────────────

    public function visualExplain(Request $request)
    {
        $defaultQuery = "SELECT * FROM products WHERE price > 500";
        $rawQuery = $request->input('query', $defaultQuery);

        // Validate: only SELECT allowed
        if (!preg_match('/^\s*SELECT\s/i', $rawQuery)) {
            return view('visual', ['error' => 'Only SELECT queries are allowed.', 'rawQuery' => $rawQuery]);
        }

        try {
            // EXPLAIN FORMAT=JSON
            $explainJson = DB::select("EXPLAIN FORMAT=JSON " . $rawQuery);
            $jsonData = json_decode($explainJson[0]->EXPLAIN, true);

            // Standard EXPLAIN for table data
            $explainRows = DB::select("EXPLAIN " . $rawQuery);

            // Measure execution time & log
            $start = microtime(true);
            DB::select($rawQuery);
            $execTime = round((microtime(true) - $start) * 1000, 2);

            $firstRow = $explainRows[0] ?? null;
            QueryPerformanceLog::create([
                'query'          => $rawQuery,
                'execution_time' => $execTime,
                'rows_examined'  => $firstRow->rows ?? 0,
                'key_used'       => $firstRow->key ?? null,
            ]);

        } catch (\Exception $e) {
            return view('visual', ['error' => $e->getMessage(), 'rawQuery' => $rawQuery]);
        }

        return view('visual', compact('rawQuery', 'jsonData', 'explainRows', 'execTime'));
    }

    // ─── FEATURE 2: Auto-Index Recommendation ────────────────────────────────────

    public function indexRecommend(Request $request)
    {
        $defaultQuery = "SELECT * FROM products WHERE price > 500";
        $rawQuery = $request->input('query', $defaultQuery);

        if (!preg_match('/^\s*SELECT\s/i', $rawQuery)) {
            return view('recommend', ['error' => 'Only SELECT queries are allowed.', 'rawQuery' => $rawQuery]);
        }

        try {
            $explainRows = DB::select("EXPLAIN " . $rawQuery);
        } catch (\Exception $e) {
            return view('recommend', ['error' => $e->getMessage(), 'rawQuery' => $rawQuery]);
        }

        $recommendations = [];
        foreach ($explainRows as $row) {
            $keyMissing  = empty($row->key);
            $rowsHigh    = ($row->rows ?? 0) > 100;
            $typeIsSlow  = in_array($row->type ?? '', ['ALL', 'index']);

            if ($keyMissing && ($rowsHigh || $typeIsSlow)) {
                // Extract WHERE columns from query
                preg_match_all('/WHERE\s+(\w+)\s*[=><!]/i', $rawQuery, $matches);
                $cols = $matches[1] ?? [];

                $recommendations[] = [
                    'table'           => $row->table,
                    'type'            => $row->type,
                    'rows'            => $row->rows,
                    'possible_keys'   => $row->possible_keys,
                    'columns'         => $cols,
                    'suggestion'      => count($cols)
                        ? 'Add index on: ' . implode(', ', $cols)
                        : 'Consider adding an index on the WHERE/JOIN column(s)',
                ];
            }
        }

        return view('recommend', compact('rawQuery', 'explainRows', 'recommendations'));
    }

    // ─── FEATURE 3: Historical Query Performance Tracker ─────────────────────────

    public function performanceHistory()
    {
        // Last 7 days grouped by date
        $history = QueryPerformanceLog::selectRaw(
            'DATE(created_at) as date,
             AVG(execution_time) as avg_time,
             MAX(execution_time) as max_time,
             COUNT(*) as total_queries'
        )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Recent 20 individual logs
        $recentLogs = QueryPerformanceLog::latest()->take(20)->get();

        return view('history', compact('history', 'recentLogs'));
    }

    // ─── FEATURE 4: Database Schema Analyzer ─────────────────────────────────────

    public function schemaAnalyzer()
    {
        $dbName = DB::getDatabaseName();

        // All tables with row counts
        $tables = DB::select("
            SELECT TABLE_NAME, TABLE_ROWS, DATA_LENGTH, INDEX_LENGTH
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = ?
            ORDER BY TABLE_ROWS DESC
        ", [$dbName]);

        // All indexes
        $indexes = DB::select("
            SELECT TABLE_NAME, INDEX_NAME, COLUMN_NAME, NON_UNIQUE
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = ?
            ORDER BY TABLE_NAME, INDEX_NAME
        ", [$dbName]);

        // Group indexes by table
        $indexMap = [];
        foreach ($indexes as $idx) {
            $indexMap[$idx->TABLE_NAME][] = $idx;
        }

        // Tables with NO non-primary index (potential bottleneck)
        $noIndexTables = [];
        foreach ($tables as $table) {
            $tblIndexes = $indexMap[$table->TABLE_NAME] ?? [];
            $nonPrimary = array_filter($tblIndexes, fn($i) => $i->INDEX_NAME !== 'PRIMARY');
            if (empty($nonPrimary) && $table->TABLE_ROWS > 50) {
                $noIndexTables[] = $table->TABLE_NAME;
            }
        }

        // Columns with no index (from products table as example)
        $unindexedColumns = DB::select("
            SELECT COLUMN_NAME
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'products'
            AND COLUMN_NAME NOT IN (
                SELECT COLUMN_NAME FROM information_schema.STATISTICS
                WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'products'
            )
        ", [$dbName, $dbName]);

        return view('schema', compact('tables', 'indexMap', 'noIndexTables', 'unindexedColumns', 'dbName'));
    }
}
