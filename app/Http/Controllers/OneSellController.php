<?php

namespace App\Http\Controllers;

use App\Helpers\Facades\OneSell;
use App\Models\Product;
use Illuminate\Http\Request;

class OneSellController extends Controller
{
    public function __invoke() {
        // $categories = OneSell::categories('mobifone');
        $insert = [];
        $page = 1;
        do {
            $products = OneSell::products('mobifone', 51407, null, 1, 10);
            $pageCount = $products['pagination']['pageCount'];
            foreach ($products['data'] as $product) {
                $insert[] = [
                    'description' => $product['description'],
                    'expiry' => $product['expiry'],
                    'id' => $product['id'],
                    'price' => $product['price'],
                    'priceNumber' => $product['priceNumber'],
                    'provider' => $product['provider'],
                    'title' => $product['title'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $page++;
        } while ($page > $pageCount);
        Product::insert($insert);
        return response('ok');
    }
}
