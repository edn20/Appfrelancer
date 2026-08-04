<?php

namespace Controllers;

use MVC\Router;
use Model\Dashboard;

class DashboardController
{
    public static function index(Router $router)
    {
        session_start();

        $anioClientes = $_GET['anio_clientes'] ?? date('Y');
        $anioClientes = filter_var($anioClientes, FILTER_VALIDATE_INT) ?: date('Y');

        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            header('Location: /login');
            exit;
        }

        $usuarioId = $_SESSION['id'];

        $aniosClientes = Dashboard::aniosClientes($usuarioId);

        if (!in_array((int) $anioClientes, $aniosClientes)) {
            $anioClientes = $aniosClientes[0] ?? date('Y');
        }

        $clientesPorMes = Dashboard::clientesPorMes($usuarioId, $anioClientes);

        $resumenGeneral = Dashboard::resumenGeneral($usuarioId);
        $resumenFinanciero = Dashboard::resumenFinanciero($usuarioId);
        $proyectosPorEstado = Dashboard::proyectosPorEstado($usuarioId);
        $proximasEntregas = Dashboard::proximasEntregas($usuarioId, 5);

        $router->render('dashboard/index', [
            'titulo' => 'Dashboard',
            'pagina' => 'dashboard',
            'resumenGeneral' => $resumenGeneral,
            'resumenFinanciero' => $resumenFinanciero,
            'proyectosPorEstado' => $proyectosPorEstado,
            'proximasEntregas' => $proximasEntregas,
            'anioClientes' => $anioClientes,
            'aniosClientes' => $aniosClientes,
            'clientesPorMes' => $clientesPorMes
        ]);
    }
}
