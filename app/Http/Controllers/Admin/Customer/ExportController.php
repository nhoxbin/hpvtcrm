<?php

namespace App\Http\Controllers\Admin\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportController extends Controller
{
    public function __invoke(Request $request)
    {
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Set document properties
        $spreadsheet->getProperties()->setCreator('hpvt.net')
                    ->setLastModifiedBy('HPVT')
                    ->setTitle('data')
                    ->setSubject('data HPVT')
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
        $spreadsheet->getActiveSheet()->getStyle('A1:I1')->applyFromArray($styleArray);
        // auto fit column to content
        foreach(range('A', 'I') as $columnID) {
          $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }
        // set the names of header cells
        $sheet->setCellValue('A1', 'Số điện thoại');
        $sheet->setCellValue('B1', 'Tên gói');
        $sheet->setCellValue('C1', 'Ngày bắt đầu');
        $sheet->setCellValue('D1', 'Ngày kết thúc');
        $sheet->setCellValue('E1', 'Gói có sẵn');
        $sheet->setCellValue('F1', 'Người làm việc');
        $sheet->setCellValue('G1', 'Trạng thái');
        $sheet->setCellValue('H1', 'Sales Ghi chú');
        $sheet->setCellValue('I1', 'Admin Ghi chú');

        $authUser = $request->user();
        if ($authUser->is_admin) {
            $customers = Customer::with('user')->get();
        }/*  else {
            $users = $authUser->created_users->pluck('id');
            $users->push($authUser->id);
            $customers = Customer::whereIn('user_id', $users)->get();
        } */
        // Add data
        $x = 2;
        foreach($customers as $customer) {
            $sheet->setCellValue('A'.$x, $customer->phone);
            $sheet->setCellValue('B'.$x, $customer->data);
            $sheet->setCellValue('C'.$x, $customer->registered_at);
            $sheet->setCellValue('D'.$x, $customer->expired_at);
            $sheet->setCellValue('E'.$x, $customer->available_data ? implode(',', $customer->available_data) : null);
            $sheet->setCellValue('F'.$x, $customer->user?->name);
            $sheet->setCellValue('G'.$x, $customer->state);
            $sheet->setCellValue('H'.$x, $customer->sales_note);
            $sheet->setCellValue('I'.$x, $customer->admin_note);
            $x++;
        }
        //Create file excel.xlsx
        $writer = new Xlsx($spreadsheet);
        $file = 'storage/data-'.date('d-m-Y', time()).'.xlsx';
        $writer->save($file);
        return response()->download($file)->deleteFileAfterSend();
    }
}
