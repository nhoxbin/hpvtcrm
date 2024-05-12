<?php

namespace App\Http\Controllers\Admin\OneBss;

use App\Http\Controllers\Controller;
use App\Models\OneBssCustomer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DistributeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        DB::beginTransaction();
        try {
            if (is_array($request->user_id) && count($request->user_id) == 1) {
                $customers = OneBssCustomer::query()
                    ->when($request->goi_data || $request->expires_in, function($query, $search) use ($request) {
                        if ($request->goi_data) {
                            $query->whereRaw('JSON_EXTRACT(`goi_data`, "$[*].PACKAGE_NAME") like "%'.$request->goi_data.'%"');
                        }
                        if ($request->expires_in) {
                            $query->whereRaw('JSON_EXTRACT(`goi_data` , "$[*].TIME_END") >= ?', [Carbon::now()->subDays($request->expires_in)]);
                        }
                    })->update(['user_id' => $request->user_id[0]]);
            } else {
                if (in_array('all', $request->user_id)) {
                    // chia đều tất cả user
                    if ($request->user()->is_admin) {
                        $users = User::all();
                    } else {
                        $users = $request->user()->created_users;
                    }
                } else {
                    // chọn nhiều user
                    $users = User::whereIn('id', $request->user_id)->get();
                }
                // đoạn code phân cho sales
                if (isset($users) && $users->count() > 0) {
                    $customers = OneBssCustomer::where('user_id', null)->get();
                    $maxLength = intval($customers->count() / $users->count()) + 1;
                    $pos = 0;
                    foreach ($users as $key => $user) {
                        $usersAfter = $users->count() - $key - 1;

                        for ($i = 0; $i < $maxLength && $pos < $customers->count(); $i++) {
                            if ($usersAfter > 0) {
                                $elCountAfter = $customers->count() - $pos - 1;
                                $maxLengthAfter = floor(($elCountAfter / $usersAfter) + 1);
                                if ($i + 1 > $maxLengthAfter) {
                                    break;
                                }
                            }
                            if (!empty($customers[$pos])) {
                                $customers[$pos]->user_id = $user->id;
                                $customers[$pos]->save();
                            }
                            $pos++;
                        }
                    }
                }
            }
            DB::commit();
            return redirect()->route('admin.onebss.customers.index', $request->all('goi_data', 'expires_in'))->with('msg', 'Đã phân phối dữ liệu đến sales.');
        } catch(\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->route('admin.onebss.customers.index', $request->all('goi_data', 'expires_in'))->withError('Lỗi rồi!');
        }
    }
}