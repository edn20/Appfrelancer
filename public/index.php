<?php

require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\AuthController;
use Controllers\ClienteController;
use Controllers\DashboardController;
use Controllers\PagoController;
use Controllers\ProyectoController;
use Controllers\TareaController;
use Controllers\NotaController;
use Controllers\ReporteController;
use Controllers\ConfiguracionController;
use Controllers\PerfilController;
use Controllers\NotificacionController;
use Controllers\UsuarioController;

$router = new Router();


// Login
$router->get('/', [AuthController::class, 'login']);
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

// Crear Cuenta
$router->get('/registro', [AuthController::class, 'registro']);
$router->post('/registro', [AuthController::class, 'registro']);

// Formulario de olvide mi password
$router->get('/olvide', [AuthController::class, 'olvide']);
$router->post('/olvide', [AuthController::class, 'olvide']);

// Colocar el nuevo password
$router->get('/reestablecer', [AuthController::class, 'reestablecer']);
$router->post('/reestablecer', [AuthController::class, 'reestablecer']);

// Confirmación de Cuenta
$router->get('/mensaje', [AuthController::class, 'mensaje']);
$router->get('/confirmar-cuenta', [AuthController::class, 'confirmar']);
$router->get('/solicitar-alta', [AuthController::class, 'solicitarAlta']);

//Dashboard
$router->get('/dashboard', [DashboardController::class, 'index']);

//Clientes
$router->get('/clientes', [ClienteController::class, 'index']);
$router->get('/clientes/crear', [ClienteController::class, 'crear']);
$router->post('/clientes/crear', [ClienteController::class, 'crear']);
$router->get('/clientes/editar', [ClienteController::class, 'editar']);
$router->post('/clientes/editar', [ClienteController::class, 'editar']);
$router->get('/clientes/detalle', [ClienteController::class, 'detalle']);
$router->post('/clientes/eliminar', [ClienteController::class, 'eliminar']);
//validaciones
$router->post('/clientes/verificar-identificacion', [ClienteController::class, 'verificarIdentificacion']);

//Proyectos
$router->get('/proyectos', [ProyectoController::class, 'index']);
$router->get('/proyectos/crear', [ProyectoController::class, 'crear']);
$router->post('/proyectos/crear', [ProyectoController::class, 'crear']);
$router->get('/proyectos/editar', [ProyectoController::class, 'editar']);
$router->post('/proyectos/editar', [ProyectoController::class, 'editar']);
$router->get('/proyectos/detalle', [ProyectoController::class, 'detalle']);
$router->post('/proyectos/eliminar', [ProyectoController::class, 'eliminar']);

//Tareas
$router->get('/tareas', [TareaController::class, 'index']);
$router->get('/tareas/crear', [TareaController::class, 'crear']);
$router->post('/tareas/crear', [TareaController::class, 'crear']);
$router->get('/tareas/editar', [TareaController::class, 'editar']);
$router->post('/tareas/editar', [TareaController::class, 'editar']);
$router->get('/tareas/detalle', [TareaController::class, 'detalle']);
$router->post('/tareas/eliminar', [TareaController::class, 'eliminar']);
$router->post('/tareas/adjunto', [TareaController::class, 'subirAdjunto']);
$router->get('/tareas/adjunto/descargar', [TareaController::class, 'descargarAdjunto']);
$router->post('/tareas/adjunto/eliminar', [TareaController::class, 'eliminarAdjunto']);

//Pagos
$router->get('/pagos', [PagoController::class, 'index']);
$router->get('/pagos/crear', [PagoController::class, 'crear']);
$router->post('/pagos/crear', [PagoController::class, 'crear']);
$router->get('/pagos/detalle', [PagoController::class, 'detalle']);
$router->post('/pagos/actualizar', [PagoController::class, 'actualizar']);
$router->get('/pagos/adjunto/descargar', [PagoController::class, 'descargarAdjunto']);

// Notas
$router->get('/notas/desbloquear', [NotaController::class, 'desbloquear']);
$router->post('/notas/verificar', [NotaController::class, 'verificar']);
$router->get('/notas', [NotaController::class, 'index']);
$router->get('/notas/crear', [NotaController::class, 'crear']);
$router->post('/notas/crear', [NotaController::class, 'crear']);
$router->get('/notas/detalle', [NotaController::class, 'detalle']);
$router->post('/notas/actualizar', [NotaController::class, 'actualizar']);
$router->post('/notas/eliminar', [NotaController::class, 'eliminar']);
//Validaciones
$router->post('/notas/desbloquear-ajax', [NotaController::class, 'desbloquearAjax']);
$router->post('/notas/bloquear-ajax', [NotaController::class, 'bloquearAjax']);

//Reportes
$router->get('/reportes', [ReporteController::class, 'index']);

//Configuracion
$router->get('/configuracion', [ConfiguracionController::class, 'index']);
$router->post('/configuracion/notificaciones', [ConfiguracionController::class, 'notificaciones']);
$router->post('/configuracion/preferencias', [ConfiguracionController::class, 'preferencias']);

//Perfil
$router->get('/perfil', [PerfilController::class, 'index']);
$router->post('/perfil', [PerfilController::class, 'index']);
$router->post('/perfil/password', [PerfilController::class, 'password']);

//Notificaciones
$router->get('/notificaciones', [NotificacionController::class, 'index']);

//Usuarios - Administrador
$router->get('/usuarios', [UsuarioController::class, 'index']);
$router->post('/usuarios/actualizar', [UsuarioController::class, 'actualizar']);




$router->comprobarRutas();
