<?php

namespace Model;

class Usuario extends ActiveRecord {
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];

    public $id;
    public $nombre;
    public $email;
    public $password;
    public $password2;
    public $token;
    public $confirmado;
    public $password_actual;
    public $password_nuevo;



    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nuevo = $args['password_nuevo'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
    }
    // validar el login
    public function validarLogin(){
        if(!$this->email){
            self::$alertas['error'][] = 'El email del usuario es obligatorio';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] ='El email no es valido';
        }
        if(!$this->password){
            self::$alertas['error'][] = 'El password no puede ir vacio';
        }
        return self::$alertas;
    }

    //validacion para cuentas nuevas
    public function ValidarCuentaNueva(){
        if(!$this->nombre){
            self::$alertas['error'][] = 'El nombre del usuario es obligatorio';
        }
        if(!$this->email){
            self::$alertas['error'][] = 'El email del usuario es obligatorio';
        }
        if(!$this->password){
            self::$alertas['error'][] = 'El password no puede ir vacio';
        }
        if(strlen($this->password) < 6){
            self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres';
        }
        if($this->password!== $this->password2){
        self::$alertas['error'][] = 'Los password no coinciden';
        }
        return self::$alertas;
    }
    //valida un password
    public function validarPassword(){
        if(!$this->password){
            self::$alertas['error'][] = 'El password no puede ir vacio';
        }
        if(strlen($this->password) < 6){
            self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres';
        }
       
        return self::$alertas;
    }

    //valida un email
    public function validarEmail(){
        if(!$this->email){
            self::$alertas['error'][]= 'El email es obligatorio';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] ='El email no es valido';
        }

        return self::$alertas;
    }
    //valida
    public function validarPerfil () {
        if(!$this->nombre){
            self::$alertas['error'][] = 'El nombre del usuario es obligatorio';
        }
        if(!$this->email){
            self::$alertas['error'][] = 'El email del usuario es obligatorio';
        }
        return self::$alertas;
    }
    public function nuevo_passowrd() :array{
        if(!$this->password_actual) {
            self::$alertas['error'][] ='El password actual no puede ir vacio';
        }
        if(!$this->password_nuevo) {
            self::$alertas['error'][] ='El password nuevo no puede ir vacio';
        }
        if(strlen($this->password_nuevo) < 6){
            self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres';
        }
       
        return self::$alertas;
    }
    public function comprobar_password() :bool{
        return password_verify($this->password_actual, $this->password );
    }

    //hashea el password
    public function hashPassword() : void{
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
        }

    //generar token
    public function crearToken() : void{
        $this->token =uniqid();
    }
}