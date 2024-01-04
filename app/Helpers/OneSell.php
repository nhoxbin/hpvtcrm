<?php

namespace App\Helpers;

use Ixudra\Curl\Facades\Curl;

class OneSell
{
    private array $providers = ['mobifone', 'viettel', 'vinaphone'];

    public function products(string $provider, int $category_id, string $search = null, int $page = 1, int $limit = 10)
    {
        if (!in_array($provider, $this->providers)) return null;

        return Curl::to(config('one_sell.endpoint') . '/v1/products')
            ->withHeaders(['Api-Key' => config('one_sell.apiKey')])
            ->withContentType('application/json')
            ->withOption('POSTFIELDS', json_encode([
                "provider" => $provider,
                "type" => "packagemobile",
                "category" => $category_id,
                "search" => $search,
                "page" => $page,
                "limit" => $limit
            ]))
            ->asJson(true)
            ->get();
    }

    public function categories(string $provider)
    {
        if (!in_array($provider, $this->providers)) return null;
        return Curl::to(config('one_sell.endpoint') . '/v1/categories')
            ->withHeaders(['Api-Key' => config('one_sell.apiKey')])
            ->withContentType('application/json')
            ->withOption('POSTFIELDS', json_encode([
                "provider" => $provider,
                "type" => "packagemobile"
            ]))
            ->asJson(true)
            ->get();
    }
}