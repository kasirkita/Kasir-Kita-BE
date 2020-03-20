<?php

namespace App;

use Jenssegers\Mongodb\Auth\User as Authenticatable;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class User extends Authenticatable
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = ['avatar', 'date_of_birth_formatted'];

    protected $dates = ['date_of_birth'];

    public function getAvatarAttribute()
    {
        return Storage::drive('images')->exists($this->photo) 
        ? url('storage/images/'.$this->photo) : null;
    }

    public function getDateOfBirthFormattedAttribute()
    {
        return Carbon::parse($this->date_of_birth)->format('F jS, Y');
    }

    public function role()
    {
        return $this->belongsTo('App\Role');
    }

}
