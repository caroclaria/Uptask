<?php
namespace Controllers;
use Model\Proyecto;
use Model\Usuario;

use MVC\Router;

class DashboardController {
    public static function index(Router $router){
        session_start();
        isAuth();

        $id=$_SESSION['id'];
        $proyectos = Proyecto::belongsTo('propietarioId', $id);

        $router->render('dashboard/index', [
            'titulo'=>'Proyectos',
            'proyectos'=>$proyectos
        ]);
    }

    public static function crear_proyecto(Router $router){
        session_start();
        isAuth();
        $alertas = [];

        if($_SERVER['REQUEST_METHOD']=== 'POST'){
            $proyecto = new Proyecto($_POST);

            //Validaion
            $alertas = $proyecto->validarProyecto();

            if(empty($alertas)){
                //generar una url unica
                $proyecto->url = md5(uniqid());
                //almacenar el creador del proyecto
                $proyecto->propietarioId =$_SESSION['id'];
                //guardar el proyecto
                $proyecto->guardar();
                //redireccionar
                header('Location:/proyecto?id=' . $proyecto->url);
            }

        }
        
        $router->render('dashboard/crear-proyecto', [
            'titulo'=>'Crear proyecto',
            'alertas'=> $alertas
        ]);
    }
    public static function proyecto (Router $router){
        session_start();
        isAuth();
        
        $token = $_GET['id'];
        if(!$token) header('Location: /dashboard');
        //revisar que la perona que hizo el proyecto
        $proyecto = Proyecto::where('url', $token);
        if($proyecto->propietarioId !== $_SESSION['id']){
            header('Location: /dashboard');
        }


        $router->render('dashboard/proyecto', [
            'titulo'=>$proyecto->proyecto
        ]);
    }

    public static function perfil(Router $router){
        session_start();
        isAuth();
        $alertas = [];
        $usuario = Usuario::find($_SESSION['id']);

        if($_SERVER['REQUEST_METHOD']=== 'POST'){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarPerfil();

            if(empty($alertas)){
                $existeUsuario = Usuario::where('email', $usuario->email);
                if($existeUsuario && $existeUsuario->id !== $usuario->id ){
                    Usuario::setAlerta('error', 'Email no valido, cuenta ya registrada');
                    $alertas = $usuario->getAlertas();
                }else {
                   //guardar el usuario
                $usuario ->guardar();

                Usuario::setAlerta('exito', 'Guardado correctamente');
                $alertas = $usuario->getAlertas();
                //asifnar nuevo nombre a la barra
                $_SESSION['nombre'] = $usuario->nombre; 
                }
                
            }
        }

        $router->render('dashboard/perfil', [
            'titulo'=>'Perfil',
            'usuario' => $usuario,
            'alertas'=> $alertas

        ]);
    }

    public static function cambiar_password(Router $router){
        session_start();
        isAuth();

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = Usuario::find($_SESSION['id']);

            // Sincronizar con los datos del usuario
            $usuario->sincronizar($_POST);

            $alertas = $usuario->nuevo_password();

            if(empty($alertas)) {
                $resultado = $usuario->comprobar_password();

                if($resultado) {
                    $usuario->password = $usuario->password_nuevo;

                    // Eliminar propiedades No necesarias
                    unset($usuario->password_actual);
                    unset($usuario->password_nuevo);

                    // Hashear el nuevo password
                    $usuario->hashPassword();

                    // Actualizar
                    $resultado = $usuario->guardar();

                    if($resultado) {
                        Usuario::setAlerta('exito', 'Password Guardado Correctamente');
                        $alertas = $usuario->getAlertas();
                    }
                } else {
                    Usuario::setAlerta('error', 'Password Incorrecto');
                    $alertas = $usuario->getAlertas();
                }
            }
        }
        
        $router->render('dashboard/cambiar_password', [
            'titulo'=>'cambiar password',
            'alertas'=> $alertas
        ]);
    }
    }