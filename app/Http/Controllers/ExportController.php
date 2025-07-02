<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MonthlyReportExport;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function monthlyReport(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $fileName = "Laporan_Bulanan_{$year}_{$month}.xlsx";
        
        return Excel::download(new MonthlyReportExport($month, $year), $fileName);
    }
}
