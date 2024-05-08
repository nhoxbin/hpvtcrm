<?php

namespace App\Http\Controllers\OneBss\Export;

use App\Http\Controllers\Controller;
use App\Models\DigiShopCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\File;

class CustomerController extends Controller
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
        $spreadsheet->getProperties()->setCreator('DigiShop')
                    ->setLastModifiedBy('DigiShop')
                    ->setTitle('data')
                    ->setSubject('data DigiShop')
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

        $customers = DigiShopCustomer::all();
        // Add data
        $x = 2;
        foreach($customers as $customer) {
            $sheet->setCellValue('A'.$x, $customer->phone_number);
            $sheet->setCellValue('B'.$x, $customer->tkc);
            $sheet->setCellValue('C'.$x, $customer?->first_product_name);
            if (!empty($customer->packages)) {
              $sheet->setCellValue('D'.$x, $customer->packages['service_name']);
              $sheet->setCellValue('E'.$x, $customer->packages['expired_at']);
            }
            $x++;
        }
        //Create file excel.xlsx
        $writer = new Xlsx($spreadsheet);
        $path = storage_path('app/public/digishop-'.date('d-m-Y', time()).'.xlsx');
        $writer->save($path);
        return response()->download($path)->deleteFileAfterSend();
    }
}
