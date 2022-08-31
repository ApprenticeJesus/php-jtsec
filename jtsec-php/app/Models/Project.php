<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';

    //Relación de uno a muchos
    public function activities(){
        return $this->hasMany('App\Models\Activity');
    }
}
