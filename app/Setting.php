<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute()
    {
        return !empty($this->logo) && Storage::disk('images')->exists($this->logo) ? url('storage/images/'.$this->logo) : url('default.png');
    }
}
