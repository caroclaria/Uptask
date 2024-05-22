<?php

namespace Controllers;
use Classes\Email;
use MVC\Router;
use Model\Usuario;

class LoginController {
    public static function login(Router $router){
      $alertas = [];

       if($_SERVER['REQUEST_METHOD']=== 'POST'){
         $usuario = new Usuario($_POST);
         $alertas= $usuario->validarLogin();

         if(empty($alertas)){
            //Verificar que el usuario exista
            $usuario = Usuario::where('email', $usuario->email);
            if(!$usuario || !$usuario->confirmado){
               Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
            } else {
               //usuario existe
               if(password_verify($_POST['password'], $usuario->password)){
                  //iniciar sesion
                  session_start();
                  $_SESSION['id'] = $usuario->id;
                  $_SESSION['nombre'] = $usuario->nombre;
                  $_SESSION['email'] = $usuario->email;
                  $_SESSION['login'] = true;
                  //redireccionar
                  header('Location: /dashboard');

               }else {
                  Usuario::setAlerta('error', 'El password es incorrecto');
               }

            }
         }

       }
       $alertas = Usuario::getAlertas();
       //render a la vista
       $router->render('auth/login', [
        'titulo'=>'Iniciar sesion',
        'alertas'=>$alertas
        ]);
       }

    public static function logout(){
      session_start();
      $_SESSION = [];
      header('Location: /');


    }
    public static function crear(Router $router){
         $alertas = [];
         $usuario = new Usuario;
 
        if($_SERVER['REQUEST_METHOD']=== 'POST'){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->ValidarCuentaNueva();

            if(empty($alertas)){
            $existeUsuario = Usuario::where('email', $usuario->email);
            if($existeUsuario) {
               Usuario::setAlerta('error', 'el usuario ya esta registrado');
               $alertas = Usuario::getAlertas();
               } else {
                  //hashear el password
                  $usuario->hashPassword();

                  //eliminar password2
                  unset($usuario->password2);

                  //generar token
                  $usuario->crearToken();

                  //crear un nuevo usuario
                  $resultado = $usuario->guardar();

                  //enviar email
                  $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                  $email->enviarConfirmacion();
                  if($resultado) {
                     header('Location: /mensaje');
                  }
               }
            }

         
        }
         //render a la vista
       $router->render('auth/crear', [
        'titulo'=>'Crear sesion',
        'usuario'=> $usuario,
        'alertas'=> $alertas
        ]);
     }
     public static function olvide(Router $router){
      $alertas = [];
        if($_SERVER['REQUEST_METHOD']=== 'POST'){
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if(empty($alertas)){
               //buscar el usuario
               $usuario = Usuario::where('email', $usuario->email);

               if($usuario && $usuario->confirmado) {
                  //generar un nuevo token
                  $usuario->crearToken();
                  unset($usuario->password2);

                  //actualizar el usuario
                  $usuario->guardar();

                  //enviar email
                  $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                  $email->enviarInstrucciones();
                  //imprimir el alerta
                  Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');
                  
               } else{
                  //no encontro el usuario
                  Usuario::setAlerta('error', 'el usuario no existe o no esta confirmado');
                 
               }
            }
        }
        $alertas= Usuario::getAlertas();
        //render a la vista
       $router->render('auth/olvide', [
         'titulo'=>'recuperarpassword',
         'alertas'=>$alertas
         ]);
     }
     public static function reestablecer(Router $router){
        $token = s($_GET['token']);
        $mostrar = true;
        if(!$token) header('Location: /');

        //identificar uuario con este token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token no valido');
            $mostrar = false;
        }

        if($_SERVER['REQUEST_METHOD']=== 'POST'){
         //añadir nuevo password
         $usuario->sincronizar($_POST);
         //validar password
        $alertas = $usuario->validarPassword();
        if(empty($alertas)){
         //hashear el nuevo password
         $usuario->hashPassword();
         //eliminar token
         $usuario->token = null;
         //guardar usuario
         $resultado = $usuario->guardar();
         //redireccionar
         if($resultado){
            header('Location: /');
         }
        }
        }
        $alertas = Usuario::getAlertas();
         //render a la vista
       $router->render('auth/reestablecer', [
         'titulo'=>'Reestablecer Password',
         'alertas'=> $alertas,
         'mostrar'=>$mostrar
         ]);
     }
     public static function mensaje(Router $router){
       //render a la vista
       $router->render('auth/mensaje', [
         'titulo'=>'Cuenta creada exitosamente'
         ]);

     }
     public static function confirmar(Router $router){
      $token = s($_GET['token']);
      $alertas = [];

      if(!$token) header('Location: /');

      //encontrar al usuario con este token
      $usuario = Usuario::where('token', $token);

      if(empty($usuario)){
         //no se encontro usuario con ese token
         Usuario::setAlerta('error', 'Token no valido');
      } else {
         //confimar la cuenta
         $usuario->confirmado = 1;
         $usuario->token = "";
         unset($usuario->password2);

         //guardar en la base de datos
         $usuario->guardar();
         Usuario::setAlerta('exito', 'Cuenta confirmada correctamente');

      }

      $alertas = Usuario::getAlertas();

        //render a la vista
       $router->render('auth/confirmar', [
         'titulo'=>'Confirmar cuenta',
         'alertas' => $alertas
         ]);

     }
}

    