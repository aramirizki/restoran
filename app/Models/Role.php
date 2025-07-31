<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use softDeletes;

    protected $fillable = ['rolename', 'description','created_at', 'updated_at'];

    protected $dates = ['deleted_at'];
    
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
