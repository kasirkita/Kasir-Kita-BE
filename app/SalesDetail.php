<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Setting;
use Carbon\Carbon;

class SalesDetail extends Model
{
    use SoftDeletes;

    protected $appends = ['price_formatted', 'discount_formatted', 'subtotal_formatted', 'cost_formatted', 'total_formatted', 'created_at_formatted'];

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

    public function getCostFormattedAttribute()
    {
        return $this->formattedValue($this->cost);
    }

    public function getTotalFormattedAttribute()
    {
        return $this->formattedValue($this->total);
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

    public function getCreatedAtFormattedAttribute()
    {
        return Carbon::parse($this->created_at)->format('m/d/Y');
    }

}
