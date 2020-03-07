<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

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
        return 'Rp. '.number_format($this->price);
    }

    public function getCostFormattedAttribute()
    {
        return 'Rp. '.number_format($this->cost);
    }

    public function getWholesaleFormattedAttribute()
    {
        return 'Rp. '.number_format($this->wholesale);
    }
}
