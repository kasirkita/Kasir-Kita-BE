<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Stock extends Model
{
    use SoftDeletes;

    protected $fillable = ['product_id'];

    public function details()
    {
        return $this->hasMany('App\StockDetail');
    }
}
