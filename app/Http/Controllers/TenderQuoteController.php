<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use App\Models\User;
use Dompdf\Dompdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RuntimeException;

class TenderQuoteController extends Controller
{
    public function pdf(Tender $tender): Response
    {
        $user = Auth::user();
        abort_unless($user instanceof User && Gate::allows('view', $tender) && ($user->isAdmin() || $tender->quote_status !== 'draft'), 403);
        $dompdf = new Dompdf;
        $dompdf->loadHtml(view('quotes.pdf', ['tender' => $tender->load(['quoteItems', 'creator'])])->render());
        $dompdf->setPaper('A4');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="quotation-'.$tender->reference_no.'.pdf"',
        ]);
    }

    public function excel(Tender $tender): Response
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->isAdmin() && $tender->quoteItems()->exists(), 403);
        $tender->load(['quoteItems', 'creator']);
        $sheet = (new Spreadsheet)->getActiveSheet();
        $sheet->fromArray([
            ['QUOTATION', null, null, null, null],
            ['Reference', $tender->reference_no, 'Customer', $tender->creator?->name, null],
            ['Customer email', $tender->creator?->email, 'Phone', $tender->creator?->phone, null],
            ['Position', $tender->creator?->position, 'Client / institution', $tender->client_name, null],
            ['Tender title', $tender->title, 'Currency', $tender->currency, null],
            [],
            ['Description', 'Unit', 'Quantity', 'Unit price', 'Total'],
        ]);
        foreach ($tender->quoteItems->sortByDesc('version')->unique('description')->reverse() as $item) {
            $sheet->fromArray([[$item->description, $item->unit, (float) $item->quantity, (float) $item->unit_price, (float) $item->quantity * (float) $item->unit_price]]);
        }
        $sheet->fromArray([[], ['Grand total including VAT (18%)', null, null, null, (float) $tender->quoted_amount]]);
        foreach (range('A', 'E') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $writer = new Xlsx($sheet->getParent());
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();
        if ($content === false) {
            throw new RuntimeException('Unable to capture the quotation export.');
        }

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="quotation-'.$tender->reference_no.'.xlsx"',
        ]);
    }
}
