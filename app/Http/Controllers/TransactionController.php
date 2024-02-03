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
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        $msg = 'Không thể đăng ký gói!';
        $validated = $request->validated();
        if ($request->user()->is_admin) {
            $customers = new Customer();
        } else {
            $customers = $request->user()->customers();
        }
        $customer = $customers->orWhere([
            ['phone', $validated['phoneNumber']],
            ['phone', str_pad($validated['phoneNumber'], 10, '0', STR_PAD_LEFT)]
        ])->firstOrCreate([
            'phone' => $validated['phoneNumber'],
        ], ['user_id' => $request->user()->id]);
        $transaction = $customer->transactions()->create(['product' => $validated['product'], 'created_by_user_id' => $request->user()->id]);
        $regis = OneSell::regis('mobifone', $request->product['id'], $transaction->id, $request->phoneNumber, $request->regisMethod);
        if (!empty($regis)) {
            $transaction->message = $regis['message'];
            if (isset($regis['result']) && $regis['result']) {
                $transaction->orderId = $regis['orderId'];
                $transaction->result = $regis['result'];
                $transaction->save();
                return response()->success(__($regis['message']), $transaction->toArray());
            }
            $transaction->save();
            $msg = __($regis['message']);
        }
        return response()->error($msg, 422);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        $confirmOtp = OneSell::confirmOtp('mobifone', $transaction->orderId, $request->otp);
        if (!empty($confirmOtp)) {
            $transaction->message = $confirmOtp['message'];

            preg_match('/(\d+)(.+)/', $transaction->product['expiry'], $expiry);
            $date_types = [
                'N' => 'Day',
                'T' => 'Month'
            ];

            if (isset($confirmOtp['result'])) {
                $transaction->result = $confirmOtp['result'];
                if ($confirmOtp['result']) {
                    $transaction->customer->sales_state = SalesStateEnum::Registered;
                    $transaction->customer->data = $transaction->product['title'];
                    $transaction->customer->registered_at = now();
                    $transaction->customer->expired_at = now()->{'add' . $date_types[$expiry[2]]}($expiry[1]);
                    $transaction->customer->save();
                    return response()->success($confirmOtp['message']);
                }
            }
            $transaction->save();
        }
        return response()->error($confirmOtp['message'] ?? 'Không thể xác minh OTP!', 422);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
