<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Customer;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class MultipleCustomersController extends Controller
{
    public function export(Request $request) {
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Set document properties
        $spreadsheet->getProperties()->setCreator('tymcrm.com')
                    ->setLastModifiedBy('TyM')
                    ->setTitle('data')
                    ->setSubject('data TyMCRM')
                    ->setDescription('Export data to Excel Work for me!');
        // add style to the header
        $styleArray = array(
          'font' => array(
            'bold' => true,
          ),
          'alignment' => array(
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
          ),
          'borders' => array(
              'bottom' => array(
                  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                  'color' => array('rgb' => '333333'),
              ),
          ),
          'fill' => array(
            'type'       => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
            'rotation'   => 90,
            'startcolor' => array('rgb' => '0d0d0d'),
            'endColor'   => array('rgb' => 'f2f2f2'),
          ),
        );
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleArray);
        // auto fit column to content
        foreach(range('A', 'J') as $columnID) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }
        // set the names of header cells
        $sheet->setCellValue('A1', 'Tên');
        $sheet->setCellValue('B1', 'CMND');
        $sheet->setCellValue('C1', 'Số tiền');
        $sheet->setCellValue('D1', 'Assigned to');
        $sheet->setCellValue('E1', 'Sales stage');

        $authUser = $request->user();
        if ($authUser->isAdmin) {
            $customers = Customer::all();
        } else {
            $users = $authUser->created_users->pluck('id');
            $users->push($authUser->id);
            $customers = Customer::whereIn('user_id', $users)->get();
        }
        // Add data
        $x = 2;
        foreach($customers as $customer) {
            $sheet->setCellValue('A'.$x, $customer->name);
            $sheet->setCellValue('B'.$x, $customer->cmnd);
            $sheet->setCellValue('C'.$x, $customer->loan_amount);
            $sheet->setCellValue('D'.$x, $customer->user->name);
            $sheet->setCellValue('E'.$x, $customer->sales_stage->name ?? null);
            $x++;
        }
        //Create file excel.xlsx
        $writer = new Xlsx($spreadsheet);
        $file = 'data-'.date('d-m-Y', time()).'.xlsx';
        $writer->save($file);
        return response()->download($file)->deleteFileAfterSend();
    }

    public function store(Request $request) {
        if ($request->user_id != 0) {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required'
            ]);
            if ($validator->fails()) {
                return response($validator->errors()->first(), 422);
            }
            // chọn nhiều user
            $user_id = explode(',', $request->user_id);
            $users = User::whereIn('id', $user_id)->get();
        } else {
            // chia đều tất cả user
            if ($request->user()->isAdmin) {
                $users = User::all();
            } else {
                $users = $request->user()->created_users;
            }
        }

        $validator = Validator::make($request->all(), [
            'excel' => 'required|file'
        ]);
        if ($validator->fails()) {
            return response($validator->errors(), 422);
        }
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
            for ($i = 1; $i < count($worksheet); $i++) {
                $aInfoCustomer = $worksheet[$i];
                if (empty($aInfoCustomer[0])) break;

                $customer = new Customer;
                $customer->name = $aInfoCustomer[0];
                $customer->cmnd = $aInfoCustomer[1];
                $customer->phone = $aInfoCustomer[2];
                $customer->loan_amount = str_replace('.', '', $aInfoCustomer[3]);

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
                        if($usersAfter > 0) {
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
            return response('Đã tải dữ liệu khách hàng lên hệ thống.', 200);
        } catch(\Exception $e) {
            dd($e);
            return response('Lỗi rồi! '.$e->getMessage(), 200);
        }
    }

    public function update(Request $request) {

    }

    public function destroy(Request $request) {
        // method post
        if (in_array('duplicate', $request->command)) {
            // xóa trùng data
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
        } elseif (in_array('all', $request->command)) {
            // xóa hết
            Customer::truncate();
        } elseif (in_array('user', $request->command)) {
            // xóa theo từng user_id
            Customer::where('user_id', $request->user_id)->delete();
        } else {
            // xóa từng trạng thái của sales
            $request->validate([
                'sales_stage' => 'exists:sales_stages,id'
            ]);
            Customer::whereIn('sales_stage_id', $request->command)->delete();
        }
        return redirect()->back()->withSuccess('Xóa dữ liệu thành công.');
    }
}
