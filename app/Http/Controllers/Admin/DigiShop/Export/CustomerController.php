<?php

namespace App\Http\Controllers\Admin\DigiShop\Export;

use App\Http\Controllers\Controller;
use App\Models\DigiShopCustomer;
use Carbon\Carbon;
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
        // set the names of header cells
        $sheet->setCellValue('A1', 'Số điện thoại');
        $sheet->setCellValue('B1', 'Tích hợp');
        $sheet->setCellValue('C1', 'Chu kỳ dài');
        $sheet->setCellValue('D1', 'Gói DATA');

        $query = $request->user()->digishop_customers();
        $customers = $query->get();
        // Add data
        $endChar = 0;
        $x = 2;
        foreach ($customers as $customer) {
            if (empty($customer->integration) && empty($customer->long_period)) continue;
            if (!empty($customer->integration) || !empty($customer->long_period) || !empty($customer->packages)) {
                $sheet->setCellValue('A' . $x, $customer->phone_number);
                $sheet->setCellValue('B' . $x, $customer->integration ? implode(',', $customer->integration) : '');
                $sheet->setCellValue('C' . $x, $customer->long_period ? implode(',', $customer->long_period) : '');
                if (!empty($customer->packages)) {
                    $str = '';
                    foreach ($customer->packages as $key => $package) {
                        $str .= $package['service_name'] . ',';
                        if (!empty($package['expired_at'])) {
                            $carbon = Carbon::createFromFormat('d/m/Y', $package['expired_at']);
                            $str .= $carbon->format('m/d/Y');
                        }
                        if ($key + 1 < count($customer->packages)) {
                            $str .= ',';
                        }
                    }
                    $sheet->setCellValue('D' . $x, $str);
                }
                $x++;
            }
        }
        $spreadsheet->getActiveSheet()->getStyle('A1:D1')->applyFromArray($styleArray);
        // auto fit column to content
        foreach (range('A', 'D') as $columnID) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }
        //Create file excel.xlsx
        $writer = new Xlsx($spreadsheet);
        $path = storage_path('app/public/digishop-' . date('d-m-Y', time()) . '.xlsx');
        $writer->save($path);
        $query->delete();
        return response()->download($path)->deleteFileAfterSend();
    }
}
