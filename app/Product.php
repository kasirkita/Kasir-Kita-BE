<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $appends = ['price_formatted'];

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
}
