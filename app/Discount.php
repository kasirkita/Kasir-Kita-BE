<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Setting;
use Carbon\Carbon;

class Discount extends Model
{
    use SoftDeletes;

    protected $fillable = ['product_id'];
    protected $appends = ['amount_formatted', 'valid_thru_formatted', 'term_formatted'];
    protected $dates = ['valid_thru'];

    public function product()
    {
        return $this->belongsTo('App\Product');
    }

    public function getAmountFormattedAttribute()
    {
        if ($this->type == 'percentage') {
            return $this->amount.'%';
        } else {
            return $this->formattedValue($this->amount);
        }
    }

    public function getTermFormattedAttribute()
    {
        $term  = $this->term == '=' ? 'Sama dengan ' : 'Lebih Dari ';
        return  $term.$this->total_qty;
    }

    public function getValidThruFormattedAttribute()
    {
        return Carbon::parse($this->valid_thru)->format('m/d/Y');
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
