<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incidence extends Model
{
    use HasFactory;

    protected $table = 'incidences';

    //Relación de uno a muchos inversa (muchos a uno)
    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    //Relación de uno a muchos inversa (muchos a uno)
    public function activity(){
        return $this->belongsTo('App\Models\Activity', 'activity_id');
    }
}
