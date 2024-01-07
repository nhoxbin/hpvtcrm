<?php

namespace App\Http\Controllers;

use App\Helpers\Facades\OneSell;
use App\Http\Requests\Admin\Customer\StoreTransactionRequest;
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
        // $request->validated();
        $transaction = Transaction::create($request->validated());
        $regis = OneSell::regis('mobifone', $transaction->id, $request->productId, $request->phoneNumber, $request->regisMethod);
        if (!empty($regis) && $regis['result']) {
            return response()->success($regis['message']);
        }
        return response()->error($regis['message']);
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
