<?php

namespace App\Imports;

use App\Product;
use App\Category;
use App\Unit;
use App\Stock;
use App\StockDetail;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToCollection, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {

        foreach ($rows as $row) {

            if (!empty($row['category'])) {
                $category = Category::firstOrNew([
                    'slug' => Str::slug($row['category'])
                ]);
                $category->name = $row['category'];
                $category->save();
            }
    
    
            if (!empty($row['unit'])) {
                $unit = Unit::firstOrNew([
                    'slug' => Str::slug($row['unit'])
                ]);
                $unit->name = $row['unit'];
                $unit->save();
            }
    
            $product = new Product;
            $product->code = (string)$row['code'];
            $product->name = $row['name'];
            $product->cost = $row['cost'];
            $product->price = $row['price'];
            $product->wholesale = $row['wholesale'];
            $product->category = $row['category'];
            $product->category_id = $category->id;
            $product->unit = $row['unit'];
            $product->unit_id = $unit->id;
            $product->stock = $row['stock'];
            $product->save();
    
    
            $stock = new Stock;
            $stock->amount = $row['stock'];
            $product->stock()->save($stock);
    
            $stock_detail = new StockDetail;
            $stock_detail->amount = $row['stock'];
            $stock_detail->description = 'Stok awal';
            $stock_detail->type = '+';
            $stock->details()->save($stock_detail);
        }
    }
}
