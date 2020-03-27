<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['type', 'allow'];

    public function children()
    {
        return $this->hasMany('App\Permission', 'parent_id', '_id');
    }
}
