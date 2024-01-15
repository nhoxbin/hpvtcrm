<?php

namespace App\Console\Commands;

use App\Helpers\Facades\OneSell;
use App\Models\Product;
use Illuminate\Console\Command;

class SyncProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync products from 1sell';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $categories = OneSell::categories('mobifone');
        $insert = [];
        $page = 1;
        do {
            $products = OneSell::products('mobifone', 51407, null, $page, 10);
            $pageCount = $products['pagination']['pageCount'];
            foreach ($products['data'] as $product) {
                $insert[] = [
                    'description' => $product['description'],
                    'expiry' => $product['expiry'],
                    'product_id' => $product['id'],
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
    }
}
