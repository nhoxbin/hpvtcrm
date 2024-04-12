<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use App\Models\DigiShopCustomer;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DigiShopController extends Controller
{
    /**
     * Handle the incoming request.
     */
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
        foreach(range('A', 'E') as $columnID) {
          $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }
        // set the names of header cells
        $sheet->setCellValue('A1', 'Số điện thoại');
        $sheet->setCellValue('B1', 'tkc');
        $sheet->setCellValue('C1', 'Top 5');
        $sheet->setCellValue('D1', 'Gói cước sử dụng');
        $sheet->setCellValue('E1', 'Ngày hết hạn');

        $authUser = $request->user();
        if ($authUser->is_admin) {
            $customers = DigiShopCustomer::with('user')->get();
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
            $x++;
        }
        //Create file excel.xlsx
        $writer = new Xlsx($spreadsheet);
        $file = 'storage/data-digishop-'.date('d-m-Y', time()).'.xlsx';
        $writer->save($file);
        return response()->download($file)->deleteFileAfterSend();
    }
}
