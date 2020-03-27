<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Setting;

class Product extends Model
{
    use SoftDeletes;

    protected $appends = ['price_formatted', 'cost_formatted', 'wholesale_formatted'];

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function unit()
    {
        return $this->belongsTo('App\Unit');
    }

    public function stock()
    {
        return $this->hasOne('App\Stock');
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

    protected function formattedValue($value)
    {
        $setting = Setting::first();

        return $setting->currency.str_replace($setting->decimal_separator.'00', '', number_format($value, 2, $setting->decimal_separator, $setting->thousand_separator));
    }
}
