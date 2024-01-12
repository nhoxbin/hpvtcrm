<?php

namespace App\Http\Controllers;

use App\Enums\SalesStateEnum;
use App\Helpers\Facades\OneSell;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Models\Customer;
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
        if ($request->user()->hasRole('Super Admin')) {
            $customers = $request->user()->customers();
        } else {
            $customers = Customer::all();
        }
        $customer = $customers->where('phone', str_pad($validated['phoneNumber'], 10, "0", STR_PAD_LEFT))->orWhere('phone', $validated['phoneNumber'])->firstOrFail();
        $transaction = $customer->transactions()->create(['product' => $validated['product']]);
        $regis = OneSell::regis('mobifone', $request->product['id'], $transaction->id, $request->phoneNumber, $request->regisMethod);
        if (!empty($regis)) {
            $transaction->message = $regis['message'];
            if ($regis['result']) {
                $transaction->orderId = $regis['orderId'];
                $transaction->result = $regis['result'];
            }
            $transaction->save();
            return response()->success(__($regis['message']), $transaction->toArray());
        }
        return response()->error('Không thể đăng ký gói!');
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
        $confirmOtp = OneSell::confirmOtp('mobifone', $transaction->orderId, $request->otp);
        if (!empty($confirmOtp)) {
            $transaction->result = $confirmOtp['result'];
            $transaction->message = $confirmOtp['message'];
            $transaction->created_by_user_id = $request->user()->id;
            $transaction->save();

            preg_match('/(\d+)(.+)/', $transaction->product['expiry'], $expiry);
            $date_types = [
                'N' => 'day',
                'T' => 'month'
            ];

            if ($confirmOtp['result']) {
                $transaction->customer->sales_state = SalesStateEnum::Registered;
                $transaction->customer->data = $transaction->product['title'];
                $transaction->customer->registered_at = now();
                $transaction->customer->expired_at = date('Y-m-d H:i:s', strtotime('+' . $expiry[1] . ' ' . $date_types[$expiry[2]]));
                $transaction->customer->save();
            }
            return response()->success($confirmOtp['message']);
        }
        return response()->error('Không thể xác minh OTP!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
