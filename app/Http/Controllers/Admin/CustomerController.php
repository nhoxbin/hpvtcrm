<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SalesStateEnum;
use App\Models\Customer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Customer\StoreCustomerRequest;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CustomerController extends Controller
{
    public function index() {
        return Inertia::render('Admin/Customer/Index', [
            'users' => User::whereDoesntHave('roles', fn ($role) => $role->where('name', 'Super Admin'))->get(),
            'customers' => Customer::with(['user'])->paginate()
        ]);
    }

    public function store(StoreCustomerRequest $request) {
        if (preg_match('/all/', $request->user_id)) {
            // chia đều tất cả user
            if ($request->user()->hasRole('Super Admin')) {
                $users = User::all();
            } else {
                $users = $request->user()->created_users;
            }
        } else {
            // chọn nhiều user
            $user_id = explode(',', $request->user_id);
            $users = User::whereIn('id', $user_id)->get();
        }

        DB::beginTransaction();
        try {
            $excel = $request->file('excel');
            $inputFileType = $excel->getClientOriginalExtension();
            $inputFileName = $excel->getRealPath();

            /**  Create a new Reader of the type defined in $inputFileType  **/
            $reader = IOFactory::createReader(ucfirst(strtolower($inputFileType)));
            /**  Advise the Reader that we only want to load cell data  **/
            $reader->setReadDataOnly(true);

            $spreadsheet = $reader->load($inputFileName);
            $worksheet = $spreadsheet->getSheet(0)->toArray();
            foreach ($worksheet as $key => $row) {
                if (empty($row[0])) break;
                if ($key == 0) continue;
                
                $customer = new Customer;
                $customer->phone = str_pad($row[0], 10, '0', STR_PAD_LEFT);
                $customer->data = $row[1];

                // registered_at
                $registered_at = DateTime::createFromFormat('d/m/Y', $row[2]);
                if (!$registered_at) {
                    if (is_numeric($row[2])) {
                        $registered_at = Date::excelToDateTimeObject($row[2]);
                    }
                }
                // expired_at
                $expired_at = DateTime::createFromFormat('d/m/Y', $row[3]);
                if (!$expired_at) {
                    if (is_numeric($row[3])) {
                        $expired_at = Date::excelToDateTimeObject($row[3]);
                    }
                }
                $customer->registered_at = $registered_at->format('Y-m-d H:i:s');
                $customer->expired_at = $expired_at->format('Y-m-d H:i:s');
                $available_data = [];
                for ($i=4; $i < 11; $i++) { 
                    if (!empty($row[$i])) {
                        $available_data[] = $row[$i];
                    }
                }
                $customer->available_data = $available_data;
                // đoạn code phân cho 1 người
                if (is_numeric($request->user_id) && $request->user_id > 0) {
                    $customer->user_id = $request->user_id;
                }
                $customer->save();
            }

            // đoạn code phân cho sales
            if (isset($users) && $users->count() > 0) {
                $customers = Customer::where('user_id', null)->get();
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
            DB::commit();
            return response()->success('Đã tải dữ liệu khách hàng lên hệ thống.');
        } catch(\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->error('Lỗi rồi!', 422);
        }
    }

    public function update() {
        
    }

    public function destroy(Request $request, Customer $customer) {
        if ($customer) {
            $customer->delete();
            return response()->success('Xóa dữ liệu thành công.');
        }

        switch ($request->command) {
            case 'duplicate':
                $customers = Customer::selectRaw('MIN(id) as id, cmnd')
                    ->groupBy('cmnd')
                    ->havingRaw('COUNT(*) > 1')
                    ->get();
                foreach ($customers as $customer) {
                    Customer::where([
                        ['cmnd', $customer->cmnd],
                        ['id', '<>', $customer->id]
                    ])->delete();
                }
                break;
            case 'all':
                Customer::truncate();
                break;
            case 'sales_state':
                $request->validate([
                    'sales_state' => 'required|in:'.implode(',', SalesStateEnum::values())
                ]);
                Customer::whereIn('sales_state', $request->command)->delete();
                break;
        }
        return response()->success('Xóa dữ liệu thành công.');
    }
}
