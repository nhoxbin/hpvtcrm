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
        Product::truncate();
        $insert = [];
        $page = 1;
        do {
            $products = OneSell::products(provider: 'mobifone', page: $page);
            foreach ($products['data'] as $product) {
                $product['product'] = json_encode($product);
                $product['created_at'] = now();
                $product['updated_at'] = now();
                $insert[] = $product;
            }
            $page++;
        } while ($page <= $products['pagination']['pageCount']);
        Product::insert($insert);
    }
}
