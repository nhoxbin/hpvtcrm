<?php

namespace App\Http\Controllers\Admin\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportController extends Controller
{
    public function __invoke(Request $request) {
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
}
