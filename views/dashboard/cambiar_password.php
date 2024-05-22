<?php include_once __DIR__ . '/header-dashboard.php'; ?>


    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <a href="/perfil" class="enlace">Volver al perfil</a>

    <form class="formulario" method="POST" action="/cambiar_password">
        <div class="campo">
            <label for="nombre">Password actual</label>
            <input
                type="password"
                name="password_actual"
                placeholder="Tu password actual"
            />
        </div>
        <div class="campo">
            <label for="nombre">Password nuevo</label>
            <input
                type="password"
                name="password_nuevo"
                placeholder="Tu nuevo password"
            />
        </div>

        <input type="submit" value="Guardar Cambios">
    </form>
</div>


<?php include_once __DIR__ . '/footer-dashboard.php'; ?>
    