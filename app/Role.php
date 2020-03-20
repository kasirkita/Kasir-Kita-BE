<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Permission;

class Role extends Model
{
    use SoftDeletes;

    public function permissions()
    {
        return $this->embedsMany(Permission::class);
    }

}
