<?php

namespace App\Exports;

use App\Exports\MenuSalesExport;
use App\Exports\ExpenseReportExport;
use App\Exports\StockMovementExport;
use App\Exports\TransactionSummaryExport;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MonthlyReportExport implements WithMultipleSheets
{
    protected $month;
    protected $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function sheets(): array
    {
        return [
            new TransactionSummaryExport($this->month, $this->year),
            new MenuSalesExport($this->month, $this->year),
            new StockMovementExport($this->month, $this->year),
            new ExpenseReportExport($this->month, $this->year),
        ];
    }
}