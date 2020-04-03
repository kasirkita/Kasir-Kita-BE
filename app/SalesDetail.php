<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Setting;

class SalesDetail extends Model
{
    use SoftDeletes;

    protected $appends = ['price_formatted', 'discount_formatted', 'subtotal_formatted'];

    public function product()
    {
        return $this->belongsTo('App\Product');
    }

    public function getPriceFormattedAttribute()
    {
        return $this->formattedValue($this->price);
    }

    public function getDiscountFormattedAttribute()
    {
        return $this->formattedValue($this->discount);
    }

    public function getSubtotalFormattedAttribute()
    {
        return $this->formattedValue($this->subtotal);
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
