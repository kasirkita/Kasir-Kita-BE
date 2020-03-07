<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect([
            [
                'code' => '1234567890',
                'name' => 'Contoh Import Data',
                'category' => 'Contoh',
                'price' => '0',
                'stock' => '0',
                'unit' => 'Contoh',
                'cost' => '0',
                'wholesale' => '0'
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'code',
            'name',
            'category',
            'price',
            'stock',
            'unit',
            'cost',
            'wholesale',
       ];

    }
}
