<?php

namespace App\Exports;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReportPurchaseExport implements FromView
{
    public $data;

    function __construct($data)
    {   
        $this->data = $data;
    }

    public function view(): View
    {
        return view('pdf.report_purchase_excel', $this->data);
    }
}
