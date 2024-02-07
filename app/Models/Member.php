<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        "profile_picture_name",
    ];

    public function getProfilePictureNameAttribute()
    {
       return explode("community/profile_picture/" , $this->profile_picture)[1] ?? "";
    }

    public function getProfilePictureAttribute($value)
    {
        if (!$value) return null;
        return asset('community/profile_picture/' . $value);
    }
}
