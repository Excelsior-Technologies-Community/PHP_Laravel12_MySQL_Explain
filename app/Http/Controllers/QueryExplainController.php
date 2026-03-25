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
