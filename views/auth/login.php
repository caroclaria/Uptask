<div class="contenedor login">
<?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Iniciar sesion</p>
<?php include_once __DIR__ . '/../templates/alertas.php'; ?>


        <form class="formulario" method="POST" action="/">
            <div class="campo">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    placeholder="Tu email"
                    name="email"
                    value="correo@correo.com"
                  />  
            </div>

            <div class="campo">
                <label for="email">Password</label>
                <input
                    type="password"
                    id="password"
                    placeholder="Tu password"
                    name="password"
                    value="123456"
                  />  
            </div>

            <input 
                type="submit"
                class="boton"
                value="Iniciar sesion"
                />
        </form>
        <div class="acciones">
            <a href="/crear">¿Aun no tienes cuenta? obtiene una</a>
            <a href="/olvide">¿Olvidaste tu password?</a>
        </div>
    </div>
</div>

