<?php

namespace App\Http\Controllers;

use App\Helpers\Facades\OneSell;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __invoke(Request $request) {
        $validated = $request->validate([
            'search' => 'nullable|string',
            'page' => 'nullable|integer',
            'limit' => 'nullable|integer',
        ]);
        // $categories = OneSell::categories('mobifone');
        $products = Product::paginate();
        return response($products);
    }
}
