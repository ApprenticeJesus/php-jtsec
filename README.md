# php-jtsec

El framework usado ha sido Laravel 9.
Las funciones del api están testadas con Postman.
He usado un host virtual para acortar las direcciones, habrá que adaptar a cada implementación las rutas.
Las direcciones para usar cada función son:

Añadir actividad a un proyecto:                          /api/nuevaActividad                      ruta por POST
Añadir incidencia a una actividad:                       /api/nuevaIncidencia                     ruta por POST
Asignar usuarios a proyectos, actividades e incidencias: /api/asignarProyecto                     ruta por POST
                                                         /api/asignarActividad                    ruta por POST
                                                         /api/asignarIncidencia                   ruta por POST
                                                         
Listar actividades en las que participa un usuario:      /api/user/listarActividades/{id}         ruta por GET
Listar incidencias a las que un usuario tiene acceso:    /api/listarIncidenciasPermitidas/{id}    ruta por GET
Listar participantes de un proyecto:                     /api/project/listarUsuarios/{id}         ruta por GET

PHPUnit está integrado, pero no he desarrollado la prueba.
