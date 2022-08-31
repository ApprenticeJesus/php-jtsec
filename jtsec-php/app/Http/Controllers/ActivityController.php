<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function pruebas(Request $request){
        return "Acción de pruebas de ACTIVITY-CONTROLLER";
    }

    public function addIncidence(){
        return "<h1>Acción de añadir incidencia a una actividad.</h1>";
    }
}
