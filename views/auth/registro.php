<main class="registro">
    <div class="registro__marca">
        <div class="registro__icono">
            <i class="bi bi-briefcase"></i>
        </div>

        <h1 class="registro__nombre">Freelance Manager EDN</h1>
        <p class="registro__texto">
            Crea tu cuenta para gestionar clientes, proyectos, tareas y cobros.
        </p>
    </div>

    <div class="registro__card">
        <h2 class="registro__heading">Crear cuenta</h2>
        <p class="registro__descripcion">Registra tus datos para acceder al sistema</p>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <form class="formulario" method="POST" action="/registro">

            <input 
                type="hidden" 
                name="id" 
                value="<?php echo $usuario->id ?? ''; ?>"
            >

            <input 
                type="hidden" 
                name="token" 
                value="<?php echo $usuario->token ?? ''; ?>"
            >

            <input 
                type="hidden" 
                name="confirmado" 
                value="<?php echo $usuario->confirmado ?? 0; ?>"
            >

            <input 
                type="hidden" 
                name="creado" 
                value="<?php echo $usuario->creado ?? ''; ?>"
            >

            <input 
                type="hidden" 
                name="actualizado" 
                value="<?php echo $usuario->actualizado ?? ''; ?>"
            >

            <div class="formulario__campo">
                <label class="formulario__label" for="nombre">Nombre</label>
                <div class="formulario__input-icono">
                    <i class="bi bi-person"></i>
                    <input
                        class="formulario__input"
                        type="text"
                        id="nombre"
                        name="nombre"
                        placeholder="Tu nombre"
                        value="<?php echo $usuario->nombre ?? ''; ?>"
                    >
                </div>
            </div>

            <div class="formulario__campo">
                <label class="formulario__label" for="apellido">Apellido</label>
                <div class="formulario__input-icono">
                    <i class="bi bi-person"></i>
                    <input
                        class="formulario__input"
                        type="text"
                        id="apellido"
                        name="apellido"
                        placeholder="Tu apellido"
                        value="<?php echo $usuario->apellido ?? ''; ?>"
                    >
                </div>
            </div>

            <div class="formulario__campo">
                <label class="formulario__label" for="email">Correo electrónico</label>
                <div class="formulario__input-icono">
                    <i class="bi bi-envelope"></i>
                    <input
                        class="formulario__input"
                        type="email"
                        id="email"
                        name="email"
                        placeholder="correo@ejemplo.com"
                        value="<?php echo $usuario->email ?? ''; ?>"
                    >
                </div>
            </div>

            <div class="formulario__campo">
                <label class="formulario__label" for="password">Contraseña</label>
                <div class="formulario__input-icono">
                    <i class="bi bi-lock"></i>
                    <input
                        class="formulario__input"
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Mínimo 6 caracteres"
                    >
                </div>
            </div>

            <div class="formulario__campo">
                <label class="formulario__label" for="password2">Confirmar contraseña</label>
                <div class="formulario__input-icono">
                    <i class="bi bi-lock-fill"></i>
                    <input
                        class="formulario__input"
                        type="password"
                        id="password2"
                        name="password2"
                        placeholder="Repite tu contraseña"
                    >
                </div>
            </div>

                        
            <input
                type="submit"
                class="formulario__submit"
                value="Crear cuenta"
            >
        </form>

        <div class="registro__footer">
            <p>¿Ya tienes una cuenta?</p>
            <a href="/login">Iniciar sesión</a>
        </div>
    </div>
</main>