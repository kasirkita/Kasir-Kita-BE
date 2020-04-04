<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Expense extends Model
{
    use SoftDeletes;
    protected $dates = ['payment_date'];
    protected $appends = [
        'total_formatted',
        'price_formatted',
        'payment_date_formatted',
        'evidence_url'
    ];

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

    public function getPriceFormattedAttribute()
    {
        return $this->formattedValue($this->price);
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

    public function getEvidenceUrlAttribute()
    {
        return Storage::drive('documents')->exists($this->evidence) 
        ? url('storage/documents/'.$this->evidence) : null;
    }

}
