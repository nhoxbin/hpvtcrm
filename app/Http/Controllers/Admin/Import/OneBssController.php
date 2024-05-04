<?php

namespace App\Http\Controllers\Admin\Import;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\OneBssCustomer;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class OneBssController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        /* if (in_array('all', $request->user_id)) {
            // chia đều tất cả user
            if ($request->user()->is_admin) {
                $users = User::all();
            } else {
                $users = $request->user()->created_users;
            }
        } else {
            // chọn nhiều user
            $users = User::whereIn('id', $request->user_id)->get();
        } */

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
            $customers = [];
            $phones = [];
            foreach ($worksheet as $key => $row) {
                if (empty($row[0])) break;
                // bỏ qua ô đầu
                if ($key == 0) continue;
                if (substr($row[0], 0, 1) == '0') {
                    $row[0] = substr($row[0], 1, strlen($row[0])-1);
                }
                $phone = str_pad($row[0], 11, '84', STR_PAD_LEFT);
                // $data = $row[1];

                // registered_at
                /* if (is_numeric($row[2])) {
                    $registered_at = Date::excelToDateTimeObject($row[2]);
                } else {
                    if (preg_match('/(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2}):(\d{2})/', trim($row[2]))) {
                        $registered_at = DateTime::createFromFormat('d/m/Y H:i:s', $row[2]);
                    } else {
                        $registered_at = DateTime::createFromFormat('d/m/Y', $row[2]);
                    }
                } */
                // expired_at
                /* if (is_numeric($row[3])) {
                    $expired_at = Date::excelToDateTimeObject($row[3]);
                } else {
                    if (preg_match('/(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2}):(\d{2})/', trim($row[3]))) {
                        $expired_at = DateTime::createFromFormat('d/m/Y H:i:s', $row[3]);
                    } else {
                        $expired_at = DateTime::createFromFormat('d/m/Y', $row[3]);
                    }
                }
                $registered_at = $registered_at->format('Y-m-d H:i:s');
                $expired_at = $expired_at->format('Y-m-d H:i:s');
                $available_data = [];
                for ($i=4; $i < 11; $i++) {
                    if (empty($row[$i])) break;
                    $available_data[] = $row[$i];
                }
                $available_data = $available_data; */
                // đoạn code phân cho 1 người
                /* if (is_numeric($request->user_id) && $request->user_id > 0) {
                    $user_id = $request->user_id;
                } */

                if (!in_array($phone, $phones)) {
                    $phones[] = $phone;
                    $customers[] = [
                        'phone' => $phone,
                        'core_balance' => 0,
                        'goi_data' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            OneBssCustomer::upsert($customers, ['phone'], ['core_balance', 'goi_data', 'created_at', 'updated_at']);

            // đoạn code phân cho sales
            /* if (isset($users) && $users->count() > 0) {
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
            } */
            DB::commit();
            return redirect()->route('admin.onebss.customers.index')->with('msg', 'Đã tải dữ liệu khách hàng lên hệ thống.');
        } catch(\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->route('admin.onebss.customers.index')->withError('Lỗi rồi!');
        }
    }
}
