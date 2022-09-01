<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IncidenceController extends Controller
{
    public function pruebas(Request $request){
        return "Acción de pruebas de INCIDENCE-CONTROLLER";
    }

    //Recogemos por formulario (en este caso usamos Postman para sustituir el front-end) 
    //el identificador de la incidencia a asignar, el id de usuario y el rol que tendrá.
    public function assignIncidence(Request $request)
    {

        //Recogemos los datos por post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params) && !empty($params_array)) {

            //Limpiamos los datos
            $params_array = array_map('trim', $params_array);

            $incd_num = $params_array['incid_id'];

            //Validamos datos
            $validate = Validator::make($params_array, [
                'incid_id'  => 'required',
                'user_id'   => 'required',
                'incid_rol'   => 'required | string'

            ]);

            if ($validate->fails()) {
                $data = array(
                    'status'    => 'error',
                    'code'      => '404',
                    'message'   => 'La incidencia no ha sido asignada.',
                    'errors'    => $validate->errors()
                );
            } else {

                //Comprobamos si existe el usuario
                $user = User::find($params_array['user_id']);

                if (!empty($user)) {

                    //Comprobamos que no está ya asignado
                    $incd_array = json_decode($user['asgnd_incs'], true);

                    $assigned = in_array($incd_num, $incd_array);
                    if ($assigned) {

                        $data = array(
                            'status'    => 'error',
                            'code'      => '400',
                            'message'   => 'El usuario ya participa en la incidencia.'
                        );
                    } else {
                        $new_incd = array();
                        $incd_update = array();


                        $new_incd = ["incd$incd_num" => "$incd_num"];

                        if (!in_array($new_incd, $incd_array)) {
                            //Asignamos valores
                            $incd_array = array_merge($incd_array, $new_incd);
                            $incd_update = json_encode($incd_array);
                            $params_array['asgnd_incs'] = ['asgnd_incs' => $incd_update];
                            $db_incd_update = $params_array['asgnd_incs'];
                            User::where('id', $user->id)->update($db_incd_update);

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
                                'message'   => 'No se a podido asignar la incidencia.'
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
