<?php

namespace App\Http\Controllers\Admin\OneBss\Import;

use App\Http\Controllers\Controller;
use App\Jobs\CheckCustomers;
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
            foreach ($worksheet as $key => $row) {
                if (empty($row[0])) break;
                // bỏ qua ô đầu
                if ($key == 0) continue;
                if (substr($row[0], 0, 1) == '0') {
                    $row[0] = substr($row[0], 1, strlen($row[0]) - 1);
                }
                $phone = str_pad($row[0], 11, '84', STR_PAD_LEFT);
                $customers[] = [
                    'phone' => $phone,
                    'core_balance' => $row[1],
                    'tra_sau' => $row[2],
                    'goi' => !empty($row[3]) ? $row[3] : null,
                    'expired_at' => !empty($row[4]) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[4])->format('Y-m-d') : null,
                    'integration' => !empty($row[5]) ? $row[5] : null,
                    'is_request' => !empty($row[3]) ? 1 : 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            OneBssCustomer::upsert($customers, ['phone'], ['tra_sau', 'goi', 'expired_at', 'integration', 'is_request', 'created_at', 'updated_at']);

            $accounts = $request->user()->onebss_accounts()->whereNotNull('access_token')->get();
            $chunks = array_chunk(array_filter($customers, function ($customer) {
                return $customer['is_request'] == 0;
            }), 4);

            foreach ($chunks as $i => $chunk) {
                $queueName = 'OneBss_' . $i . '_' . now()->getTimestamp();
                // 1 tài khoản check 4 thuê bao
                dispatch(new CheckCustomers($accounts[$i % count($accounts)], $chunk))->delay(now()->addSeconds(($i * 60 * 2) / count($accounts)))->onQueue($queueName);
            }
            DB::commit();
            return redirect()->route('admin.onebss.customers.index')->with('msg', 'Đã tải dữ liệu khách hàng lên hệ thống.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->route('admin.onebss.customers.index')->withError('Lỗi!!! Vui lòng liên hệ Admin.');
        }
    }
}
