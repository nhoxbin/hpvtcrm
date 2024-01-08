<?php

namespace App\Http\Controllers;

use App\Helpers\Facades\OneSell;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        $validated = $request->validated();
        $customer = $request->user()->customers()->where('phone', $validated['phoneNumer'])->firstOrFail();
        $transaction = $customer->transaction()->create(['product' => $validated['product']]);
        $regis = OneSell::regis('mobifone', $request->productId, $transaction->id, $request->phoneNumber, $request->regisMethod);
        dd($regis);
        if (!empty($regis)) {
            $msg = $regis['message'];
            $transaction->message = $msg;
            $transaction->orderId = $regis['orderId'];
            $transaction->result = $regis['result'];
            $transaction->save();

            if ($regis['result']) {
                return response()->success($msg);
            }
        }
        return response()->error($msg ?? 'Không lấy được dữ liệu!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
