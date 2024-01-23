<?php

namespace App\Http\Controllers;

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
        $products = Product::query()->when($request->search, function($query, $search) {
            $query->where('title', 'like', '%' . $search . '%');
        })->paginate(15)->withQueryString();
        // return Inertia::render('Product/Index', compact('products'));
        return response($products);
    }
}
