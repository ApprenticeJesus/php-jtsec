<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use App\Models\Incidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ActivityController extends Controller
{
    public function pruebas(Request $request){
        return "Acción de pruebas de ACTIVITY-CONTROLLER";
    }

    public function addIncidence(Request $request){
        //Recogemos los datos por activity
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params) && !empty($params_array)) {

            //Limpiamos los datos
            $params_array = array_map('trim', $params_array);

            //Validamos datos
            $validate = Validator::make($params_array, [
                'activity_id' => 'required',
                'user_id'     => 'required',
                'title'       => 'required | string',
                'content'     => 'required | string',
                
            ]);

            if($validate->fails()){
                //La validación ha fallado
                $data = array(
                    'status'    => 'error',
                    'code'      => 400,
                    'message'   => 'La incidencia no se ha creado.',
                    'errors'    => $validate->errors()
                );
            }else{
                //Creamos la actividad
                $incidence = new Incidence();
                $incidence->activity_id = $params_array['activity_id'];
                $incidence->user_id = $params_array['user_id'];
                $incidence->title = $params_array['title'];
                $incidence->content = $params_array['content'];
                $incidence->assgnd_users = '{}';

                //Guardamos la actividad
                $incidence->save();

                $data = array(
                    'status'    => 'success',
                    'code'      => 200,
                    'message'   => 'La actividad se ha creado correctamente.',
                    'incidence'  => $incidence
                );
            }
        }else{
            $data = array(
                'status'    => 'error',
                'code'      => 400,
                'message'   => 'Los datos enviados no son correctos.'
            );
        }

        return response()->json($data, $data['code']);
    }

    //Recogemos por formulario (en este caso usamos Activityman para sustituir el front-end) 
    //el identificador de la actividad a asignar, el id de usuario y el rol que tendrá.
    public function assignActivity(Request $request)
    {
        $user = new User;

        //Recogemos los datos por activity
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params) && !empty($params_array)) {

            //Limpiamos los datos
            $params_array = array_map('trim', $params_array);

            $actv_num = $params_array['actvt_id'];

            //Validamos datos
            $validate = Validator::make($params_array, [
                'actvt_id'  => 'required',
                'user_id'   => 'required',
                'actvt_rol'   => 'required | string'

            ]);

            if ($validate->fails()) {
                $data = array(
                    'status'    => 'error',
                    'code'      => '404',
                    'message'   => 'La actividad no ha sido asignada.',
                    'errors'    => $validate->errors()
                );
            } else {

                //Comprobamos si existe el usuario
                $user = User::find($params_array['user_id']);

                if (!empty($user)) {

                    //Comprobamos que no está ya asignado
                    $actv_array = json_decode($user['asgnd_acts'], true);

                    $assigned = in_array($actv_num, $actv_array);
                    if ($assigned) {

                        $data = array(
                            'status'    => 'error',
                            'code'      => '400',
                            'message'   => 'El usuario ya participa en La actividad.'
                        );
                    } else {
                        $new_actv = array();
                        $actv_update = array();


                        $new_actv = ["actv$actv_num" => "$actv_num"];

                        if (!in_array($new_actv, $actv_array)) {
                            //Asignamos valores en tabla users
                            $actv_array = array_merge($actv_array, $new_actv);
                            $actv_update = json_encode($actv_array);
                            $params_array['asgnd_acts'] = ['asgnd_acts' => $actv_update];
                            $db_actv_update = $params_array['asgnd_acts'];
                            User::where('id', $user->id)->update($db_actv_update);

                            //Asignamos valores en tabla activities
                            
                            $activity = Activity::find($params_array['actvt_id']);

                            $new_user = [$user->name => $params_array['actvt_rol']];
                            $users_array = json_decode($activity['assgnd_users'], true);
                            $users_array = array_merge($users_array, $new_user);
                            $users_update = json_encode($users_array);
                            $params_array['assgnd_users'] = ['assgnd_users' => $users_update];
                            $db_users_update = $params_array['assgnd_users'];
                            Activity::where('id', $activity->id)->update($db_users_update);



                            //Devolvemos array con el resultado
                            $data = array(
                                'status'    => 'success',
                                'code'      => 200,
                                'changes'   => $params_array
                            );
                        } else {
                            $data = array(
                                'status'    => 'error',
                                'code'      => '404',
                                'message'   => 'No se a podido asignar la actividad.'
                            );
                        }
                    }
                } else {
                    $data = array(
                        'status'    => 'error',
                        'code'      => '404',
                        'message'   => 'El usuario no existe.'
                    );
                }
            }
        } else {
            $data = array(
                'status'    => 'error',
                'code'      => '404',
                'message'   => 'No se han proporcionado los datos necesarios.',
            );
        }

        //Devolvemos respuesta
        return response()->json($data, $data['code']);
    }

    public function listGrantedIncidences($id){

        $user = User::find($id);

        $activities = json_decode($user->asgnd_acts);
        $incidences = json_decode($user->asgnd_incs);

        $incidencesGranted = [];

        foreach($activities as $activity){

            $activity_array = Activity::find($activity);

            $activity_data = [$activity_array->id => $activity_array->name];

            $activity_users = json_decode($activity_array->assgnd_users);

            foreach($activity_users as $rol){

                if($rol == 'responsable'){
                    $incidencesGranted = array_merge($incidencesGranted, $activity_data);
                }

                foreach($incidencesGranted as $incidenceGranted){
                    echo "$incidenceGranted\n";
                }
            }
        }


    }

}
