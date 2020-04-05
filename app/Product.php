<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Setting;

class Product extends Model
{
    use SoftDeletes;

    protected $appends = ['price_formatted', 'cost_formatted', 'wholesale_formatted'];
    protected $casts = [
        'code' => 'string'
    ];
    protected $fillable = ['code'];

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function unit()
    {
        return $this->belongsTo('App\Unit');
    }

    public function qty()
    {
        return $this->hasOne('App\Stock');
    }

    public function discount()
    {
        return $this->hasOne('App\Discount');
    }

    public function sales_details()
    {
        return $this->hasMany('App\SalesDetail')->groupBy('qty');
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
        if (is_numeric($value) && floor($value) != $value) {
            return $setting->currency.number_format($value, 2, !empty($setting->decimal_separator) ? $setting->decimal_separator : '' , !empty($setting->thousand_separator) ? $setting->thousand_separator : '');
        } else {
            return $setting->currency.number_format($value, 0, !empty($setting->decimal_separator) ? $setting->decimal_separator : '' , !empty($setting->thousand_separator) ? $setting->thousand_separator : '');
        }
    }
}
