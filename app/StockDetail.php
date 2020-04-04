<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use Carbon\Carbon;

class StockDetail extends Model
{
    use SoftDeletes;

    protected $appends = [
        'created_at_formatted'
    ];


    public function stock()
    {
        return $this->belongsTo('App\Stock');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function getCreatedAtFormattedAttribute()
    {
        return Carbon::parse($this->created_at)->format('m/d/Y');
    }
}
