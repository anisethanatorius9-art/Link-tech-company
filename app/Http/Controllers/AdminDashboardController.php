<?php

namespace App\Http\Controllers;

use App\Models\ProcurementRequest;
use App\Models\User;
use Dompdf\Dompdf;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'users' => User::query()->count(),
            'admins' => User::query()->where('role', 'admin')->count(),
            'userList' => User::query()->latest('created_at')->limit(12)->get(),
            'requests' => ProcurementRequest::query()->count(),
            'pending' => ProcurementRequest::query()->where('status', 'pending')->count(),
            'completed' => ProcurementRequest::query()->where('status', 'completed')->count(),
            'recentRequests' => ProcurementRequest::query()->latest()->with('user')->limit(8)->get(),
        ]);
    }

    public function exportRequests(): StreamedResponse
    {
        $requests = ProcurementRequest::query()->with('user')->latest()->get();
        $lastRow = 4 + $requests->count();

        return response()->streamDownload(function () use ($requests, $lastRow): void {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Ventis POS - Procurement Requests']);
            fputcsv($output, ['Generated at', now()->toDateTimeString()]);
            fputcsv($output, []);
            fputcsv($output, ['ID', 'Requester', 'Email', 'Type', 'Title', 'Category', 'Quantity', 'Status', 'Created at']);

            foreach ($requests as $request) {
                fputcsv($output, [$request->id, $request->user?->name, $request->user?->email, $request->type, $request->title, $request->category, $request->quantity ?? 0, $request->status, $request->created_at?->format('Y-m-d H:i:s')]);
            }

            fputcsv($output, []);
            fputcsv($output, ['Automatic totals', 'Value']);
            fputcsv($output, ['Total requests', '=COUNTA(A5:A'.$lastRow.')']);
            fputcsv($output, ['Total quantity requested', '=SUM(G5:G'.$lastRow.')']);
            fputcsv($output, ['Pending requests', '=COUNTIF(H5:H'.$lastRow.',"pending")']);
            fputcsv($output, ['Completed requests', '=COUNTIF(H5:H'.$lastRow.',"completed")']);
            fclose($output);
        }, 'ventis-procurement-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportRequestsExcel(): BinaryFileResponse
    {
        $requests = ProcurementRequest::query()->with('user')->latest()->get();
        $spreadsheet = new Spreadsheet;
        $public = $spreadsheet->getActiveSheet()->setTitle('Quotation');
        $this->buildQuotationSheet($public, $requests, false);

        $internal = $spreadsheet->createSheet()->setTitle('Internal Margin');
        $this->buildQuotationSheet($internal, $requests, true);

        $path = storage_path('app/quotation-'.now()->format('YmdHis').'.xlsx');
        (new Xlsx($spreadsheet))->save($path);

        return response()->download($path, 'link-tech-quotation-'.now()->format('Y-m-d').'.xlsx')->deleteFileAfterSend(true);
    }

    public function exportRequestsPdf()
    {
        $requests = ProcurementRequest::query()->with('user')->latest()->get();
        $html = view('reports.link-tech-quotation', ['requests' => $requests])->render();
        $dompdf = new Dompdf;
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="link-tech-quotation-'.now()->format('Y-m-d').'.pdf"',
        ]);
    }

    private function buildQuotationSheet($sheet, $requests, bool $internal): void
    {
        $sheet->mergeCells('A1:'.($internal ? 'J' : 'H').'1');
        $sheet->setCellValue('A1', 'LINK-TECH COMPANY LIMITED');
        $sheet->setCellValue('A2', 'Bagamoyo Road, Mbezi Park-Mbezi Beach/Plot No.555/C/1st Floor | P.O. Box 33225, Dar es Salaam');
        $sheet->setCellValue('A3', 'TIN NO. 106-215-448 | VRN 40-019588-0 | Mob: +255 717007797 / +255 787255353');
        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getStyle('A1')->getFont()->setSize(18)->setBold(true)->getColor()->setARGB('1D27D8');
        $headers = $internal
            ? ['S/N', 'PRODUCT DESCRIPTION', 'QTY', 'UNIT COST (TSH)', 'UNIT PRICE (TSH)', 'VAT %', 'SUBTOTAL (TSH)', 'VAT (TSH)', 'TOTAL (TSH)', 'PROFIT (TSH)']
            : ['S/N', 'PRODUCT DESCRIPTION', 'QTY', 'UNIT PRICE (TSH)', 'VAT %', 'SUBTOTAL (TSH)', 'VAT (TSH)', 'TOTAL (TSH)'];
        $headerRow = 5;
        foreach ($headers as $column => $header) {
            $sheet->setCellValue([$column + 1, $headerRow], $header);
        }
        $row = $headerRow + 1;
        foreach ($requests as $index => $request) {
            $quantity = (int) ($request->quantity ?: 1);
            $unitPrice = (float) ($request->unit_price ?: 0);
            $subtotal = $quantity * $unitPrice;
            $vat = $subtotal * ((float) $request->vat_percent / 100);
            $values = $internal
                ? [$index + 1, $request->title, $quantity, (float) ($request->unit_cost ?: 0), $unitPrice, (float) $request->vat_percent, $subtotal, $vat, $subtotal + $vat, $subtotal - ($quantity * (float) ($request->unit_cost ?: 0))]
                : [$index + 1, $request->title, $quantity, $unitPrice, (float) $request->vat_percent, $subtotal, $vat, $subtotal + $vat];
            foreach ($values as $column => $value) {
                $sheet->setCellValue([$column + 1, $row], $value);
            }
            $row++;
        }
        $sheet->getStyle('A1:'.($internal ? 'J5' : 'H5'))->getFont()->setBold(true);
        $sheet->getHeaderFooter()->setOddHeader('&C&"Arial,Bold"LINK-TECH COMPANY LIMITED');
        $sheet->getStyle('A5:'.($internal ? 'J5' : 'H5'))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('DCE3D8');
        foreach (range('A', $internal ? 'J' : 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }
}
