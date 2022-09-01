<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function pruebas(Request $request)
    {
        return "Acción de pruebas de PROJECT-CONTROLLER";
    }

    public function addActivity(Request $request)
    {
        //Recogemos los datos por post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params) && !empty($params_array)) {

            //Limpiamos los datos
            $params_array = array_map('trim', $params_array);

            //Validamos datos
            $validate = Validator::make($params_array, [
                'name'           => 'required | string',
                'project_id'     => 'required'

            ]);

            if($validate->fails()){
                //La validación ha fallado
                $data = array(
                    'status'    => 'error',
                    'code'      => 400,
                    'message'   => 'La actividad no se ha creado.',
                    'errors'    => $validate->errors()
                );
            }else{
                //Creamos la actividad
                $activity = new Activity();
                $activity->name = $params_array['name'];
                $activity->project_id = $params_array['project_id'];
                $activity->assgnd_users = '{}';

                //Guardamos la actividad
                $activity->save();

                $data = array(
                    'status'    => 'success',
                    'code'      => 200,
                    'message'   => 'La actividad se ha creado correctamente.',
                    'activity'  => $activity
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

    //Recogemos por formulario (en este caso usamos Postman para sustituir el front-end) 
    //el identificador del proyecto a asignar, el id de usuario y el rol que tendrá.
    public function assignProject(Request $request)
    {
        $user = new User;

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

                            //Asignamos valores en tabla projects

                            $project = Project::find($params_array['project_id']);

                            $new_user = [$user->id => $user->name];
                            $users_array = json_decode($project['assgnd_users'], true);
                            $users_array = array_merge($users_array, $new_user);
                            $users_update = json_encode($users_array);
                            $params_array['assgnd_users'] = ['assgnd_users' => $users_update];
                            $db_users_update = $params_array['assgnd_users'];
                            Project::where('id', $project->id)->update($db_users_update);

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

    public function getUsersByProject($id){        

        $project = Project::find($id);

        $users_json = $project['assgnd_users'];
        $users = json_decode($users_json, true);

        if(count($users) > 0){
            return \response()->json([
            'status' => 'success',
            'users'  => $users
        ], 200);
        }else{
            return \response()->json([
                'status' => 'error',
                'mesagge'  => 'El usuario no existe o no tiene actividades asignadas.'
            ], 400);
        }
    }
}
