<?php

namespace App\Http\Controllers;

use App\Helpers\Facades\OneSell;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function __invoke(Request $request) {
        $validated = $request->validate([
            'search' => 'nullable|string',
        ]);
        // $categories = OneSell::categories('mobifone');
        $products = Product::where('title', 'like', '%' . $validated['search'] . '%')->paginate(12);
        // return Inertia::render('Product/Index', compact('products'));
        return response($products);
    }
}
