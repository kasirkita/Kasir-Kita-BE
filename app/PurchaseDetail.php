<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Setting;

class PurchaseDetail extends Model
{
    use SoftDeletes;
    protected $appends = [
        'price_formatted',
        'cost_formatted',
        'wholesale_formatted',
        'subtotal_formatted'
    ];

    public function purchase()
    {
        return $this->belongsTo('App\Purchase');
    }

    public function getPriceFormattedAttribute()
    {
        return $this->formattedValue($this->price);
    }

    public function getCostFormattedAttribute()
    {
        return $this->formattedValue($this->cost);
    }

    public function getWholesaleFormattedAttribute()
    {
        return $this->formattedValue($this->wholesale);
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
