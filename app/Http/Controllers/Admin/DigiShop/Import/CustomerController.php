<?php

namespace App\Http\Controllers\Admin\DigiShop\Import;

use App\Http\Controllers\Controller;
use App\Models\DigiShopCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CustomerController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
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
                    $row[0] = substr($row[0], 1, strlen($row[0])-1);
                }
                $phone = str_pad($row[0], 11, '84', STR_PAD_LEFT);
                $customers[] = [
                    'phone_number' => $phone,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'user_id' => $request->user()->id
                ];
            }
            DigiShopCustomer::upsert($customers, ['phone_number'], ['created_at', 'updated_at']);
            return redirect()->route('admin.digishop.customers.index')->with('msg', 'Đã tải dữ liệu khách hàng lên hệ thống.');
        } catch(\Exception $e) {
            Log::error($e);
            return redirect()->route('admin.digishop.customers.index')->withError('Lỗi!!! Vui lòng liên hệ Admin.');
        }
    }
}
