<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class QueryExplainController extends Controller
{
    public function index(Request $request)
    {
        // SEARCH
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

        // PAGINATION
        $products = $productsQuery->orderBy('id', 'asc')->paginate(3);

        // STATS
        $totalProducts = Product::count();
        $highPriceProducts = Product::where('price', '>', 500)->count();

        return view('explain', compact(
            'products',
            'search',
            'totalProducts',
            'highPriceProducts'
        ));
    }

    // ADD PRODUCT
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric'
        ]);

        Product::create($request->all());

        return redirect()->back()->with('success', 'Product added successfully!');
    }

    // DELETE PRODUCT
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Product deleted successfully!');
    }
}