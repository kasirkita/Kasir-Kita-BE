<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;
use App\Setting;

class ProductUnit extends Model
{
    protected $fillable = ['product_id', 'unit_id'];
    protected $appends = ['price_formatted', 'wholesale_formatted'];

    public function product()
    {
        return $this->belongTo('App\Product');
    }

    public function unit()
    {
        return $this->belongsTo('App\Unit');
    }

    public function getPriceFormattedAttribute()
    {
        return $this->formattedValue($this->price);
    }

    public function getWholesaleFormattedAttribute()
    {
        return $this->formattedValue($this->wholesale);
    }

    protected function formattedValue($value)
    {
        $setting = Setting::first();
        if (is_numeric($value) && floor($value) != $value) {
            return $setting->currency.number_format($value, 2, !empty($setting->decimal_separator) ? $setting->decimal_separator : '' , !empty($setting->thousand_separator) ? $setting->thousand_separator : '');
        } else {
            return $setting->currency.number_format($value, 0, !empty($setting->decimal_separator) ? $setting->decimal_separator : '' , !empty($setting->thousand_separator) ? $setting->thousand_separator : '');
        }
    }
}
