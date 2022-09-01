<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function pruebas(Request $request)
    {
        return "Acción de pruebas de PROJECT-CONTROLLER";
    }

    public function addActivity()
    {
        return "<h1>Acción de añadir actividad a un proyecto.</h1>";
    }

    //Recogemos por formulario (en este caso usamos Postman para sustituir el front-end) 
    //el identificador del proyecto a asignar, el id de usuario y el/los roles que tendrá.
    public function assignProject(Request $request)
    {

        //Recogemos los datos por post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params) && !empty($params_array)) {

            //Limpiamos los datos
            $params_array = array_map('trim', $params_array);

            $proj_num = $params_array['project_id'];

            //Validamos datos
            $validate = Validator::make($params_array, [
                'project_id' => 'required',
                'user_id'    => 'required',
                'proj_rol'   => 'required | string'

            ]);

            if ($validate->fails()) {
                $data = array(
                    'status'    => 'error',
                    'code'      => '404',
                    'message'   => 'El proyecto no ha sido asignado.',
                    'errors'    => $validate->errors()
                );
            } else {

                //Comprobamos si existe el usuario
                $user = User::find($params_array['user_id']);

                if (!empty($user)) {

                    //Comprobamos que no está ya asignado
                    $proj_array = json_decode($user['asgnd_prjs'], true);

                    $assigned = in_array($proj_num, $proj_array);
                    if ($assigned) {

                        $data = array(
                            'status'    => 'error',
                            'code'      => '400',
                            'message'   => 'El usuario ya participa en el proyecto.'
                        );
                    } else {
                        $new_proj = array();
                        $proj_update = array();


                        $new_proj = ["proj$proj_num" => "$proj_num"];

                        if (!in_array($new_proj, $proj_array)) {
                            //Asignamos valores
                            $proj_array = array_merge($proj_array, $new_proj);
                            $proj_update = json_encode($proj_array);
                            $params_array['asgnd_prjs'] = ['asgnd_prjs' => $proj_update];
                            $db_proj_update = $params_array['asgnd_prjs'];
                            User::where('id', $user->id)->update($db_proj_update);

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
                                'message'   => 'No se a podido asignar el proyecto.'
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
