<?php

namespace App\Http\Controllers\Admin\OneBss\Export;

use App\Http\Controllers\Controller;
use App\Models\OneBssCustomer;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
        $spreadsheet->getProperties()->setCreator('OneBss')
            ->setLastModifiedBy('OneBss')
            ->setTitle('data')
            ->setSubject('data OneBss')
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
        foreach (range('A', 'D') as $columnID) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }
        // set the names of header cells
        $sheet->setCellValue('A1', 'Số điện thoại');
        $sheet->setCellValue('B1', 'Tài khoản chính');
        $sheet->setCellValue('C1', 'Loại thuê bao');
        $sheet->setCellValue('D1', 'Gói data');

        $customers = OneBssCustomer::where('is_request', 1)->get();
        // Add data
        $x = 2;
        foreach ($customers as $customer) {
            $sheet->setCellValue('A' . $x, $customer->phone);
            $sheet->setCellValue('B' . $x, $customer->core_balance);
            $sheet->setCellValue('C' . $x, $customer->tra_sau ? 'Trả sau' : 'Trả trước');
            if (!empty($customer->goi_data)) {
                $strData = implode("\n", array_map(function ($goi) {
                    return 'Tên gói: ' . $goi['PACKAGE_NAME'] . ', dịch vụ: ' . $goi['SERVICES'] . ', Ngày hết hạn: ' . $goi['TIME_END'];
                }, $customer->goi_data));
                $sheet->setCellValue('D' . $x, $strData);
            }
            $x++;
        }
        //Create file excel.xlsx
        $writer = new Xlsx($spreadsheet);
        $path = storage_path('app/public/onebss-' . date('d-m-Y', time()) . '.xlsx');
        $writer->save($path);
        return response()->download($path)->deleteFileAfterSend();
    }
}
