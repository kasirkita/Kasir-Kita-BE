<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Setting;

class Purchase extends Model
{
    use SoftDeletes;
    protected $dates = ['payment_date'];
    protected $appends = [
        'total_formatted',
        'subtotal_formatted',
        'total_discount_formatted',
        'tax_formatted',
        'payment_date_formatted'
    ];

    public function details()
    {
        return $this->hasMany('App\PurchaseDetail');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Supplier');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function in_charge()
    {
        return $this->belongsTo('App\User', 'in_charge_id');
    }

    public function getTotalFormattedAttribute()
    {
        return $this->formattedValue($this->total);
    }

    public function getSubTotalFormattedAttribute()
    {
        return $this->formattedValue($this->subtotal);
    }

    public function getTotalDiscountFormattedAttribute()
    {
        return $this->formattedValue($this->total_discount);
    }

    public function getTaxFormattedAttribute()
    {
        return $this->formattedValue($this->tax);
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

    public function getPaymentDateFormattedAttribute()
    {
        return Carbon::parse($this->payment_date)->format('m/d/Y');
    }
}
