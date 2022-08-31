<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Incidence;

class PruebasController extends Controller
{
    public function testOrm(){

        $incidences = Incidence::all();

        
/*
        foreach($incidences as $incidence){
            
            $name = $incidence->user->name? $incidence->user->name:'Jesús';
            
            echo "<h1>".$incidence->title."</h1>";
            echo "<h2 style='color:green;'>{$incidence->user->id} - {$name} -<span style='color:blue;'>{$incidence->activity->name}</span></h2>";
            echo "<h3>".$incidence->content."</h3>";
            echo "<hr>";
        }*/

        $activities = Activity::all();
        foreach($activities as $activity){
            echo "<h1>{$activity->name}</h1>";

            foreach($activity->incidences as $incidence){
            
                $name = $incidence->user->name? $incidence->user->name:'Jesús';
                
                echo "<h2>".$incidence->title."</h2>";
                echo "<h3 style='color:green;'>{$incidence->user->id} - {$name} -
                <span style='color:blue;'>{$incidence->activity->name}</span></h3>";
                echo "<h3>".$incidence->content."</h3>";
                
            }
            echo "<hr>";
        }

        die();
    }
}
