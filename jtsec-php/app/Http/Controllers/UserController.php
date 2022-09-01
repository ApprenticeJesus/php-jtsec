<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function getActivitiesByUser($id){        

        $user = User::find($id);

        $activities_json = $user['asgnd_acts'];
        $activities = json_decode($activities_json, true);

        if(count($activities) > 0){
            return \response()->json([
            'status' => 'success',
            'activities'  => $activities
        ], 200);
        }else{
            return \response()->json([
                'status' => 'error',
                'mesagge'  => 'El usuario no existe o no tiene actividades asignadas.'
            ], 400);
        }
    }
}
