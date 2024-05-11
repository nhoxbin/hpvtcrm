<?php

namespace App\Http\Controllers\Admin\OneBss\Import;

use App\Http\Controllers\Controller;
use App\Models\OneBssCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CustomerController extends Controller
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
                if (!in_array($phone, $phones)) {
                    $phones[] = $phone;
                    $customers[] = [
                        'phone' => $phone,
                        'is_request' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            OneBssCustomer::upsert($customers, ['phone'], ['created_at', 'updated_at']);
            DB::commit();
            return redirect()->route('admin.onebss.customers.index')->with('msg', 'Đã tải dữ liệu khách hàng lên hệ thống.');
        } catch(\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->route('admin.onebss.customers.index')->withError('Lỗi!!! Vui lòng liên hệ Admin.');
        }
    }
}
