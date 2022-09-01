<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ActivityController extends Controller
{
    public function pruebas(Request $request){
        return "Acción de pruebas de ACTIVITY-CONTROLLER";
    }

    public function addIncidence(){
        return "<h1>Acción de añadir incidencia a una actividad.</h1>";
    }

    //Recogemos por formulario (en este caso usamos Postman para sustituir el front-end) 
    //el identificador de la actividad a asignar, el id de usuario y el rol que tendrá.
    public function assignActivity(Request $request)
    {

        //Recogemos los datos por post
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
}
