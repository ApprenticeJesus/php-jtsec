<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function pruebas(Request $request){
        return "Acción de pruebas de PROJECT-CONTROLLER";
    }

    public function addActivity(){
        return "<h1>Acción de añadir actividad a un proyecto.</h1>";
    }
}
