<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'activities';

    //Relación de uno a muchos
    public function incidences(){
        return $this->hasMany('App\Models\Incidence');
    }

    //Relación de uno a muchos inversa (muchos a uno)
    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }

}
