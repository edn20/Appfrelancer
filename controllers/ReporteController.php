<?php

namespace Controllers;

use MVC\Router;
use Model\Reporte;

class ReporteController
{
    public static function index(Router $router)
    {
        session_start();

        if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
            header('Location: /login');
            exit;
        }

        $usuarioId = $_SESSION['id'];

        $seccion = $_GET['seccion'] ?? 'general';

        $seccionesPermitidas = ['general', 'financiero', 'clientes', 'proyectos'];

        if (!in_array($seccion, $seccionesPermitidas)) {
            $seccion = 'general';
        }

        $resumenGeneral = Reporte::resumenGeneral($usuarioId);
        $mesesFinanciero = $_GET['meses'] ?? 6;
        $mesesFinanciero = filter_var($mesesFinanciero, FILTER_VALIDATE_INT) ?: 6;

        $opcionesMeses = [3, 6, 12, 24];

        if (!in_array($mesesFinanciero, $opcionesMeses)) {
            $mesesFinanciero = 6;
        }
        $reporteFinanciero = Reporte::reporteFinanciero($usuarioId, $mesesFinanciero);
        $ingresosMensuales = Reporte::ingresosMensuales($usuarioId, $mesesFinanciero);
        $analisisIngresos = Reporte::analisisIngresosMensuales($ingresosMensuales);
        $reporteClientes = Reporte::reporteClientes($usuarioId);
        $reporteProyectos = Reporte::reporteProyectos($usuarioId);
        $proyectosPorEstado = Reporte::proyectosPorEstado($usuarioId);

        $router->render('reportes/index', [
            'titulo' => 'Reportes',
            'pagina' => 'reportes',
            'seccion' => $seccion,
            'resumenGeneral' => $resumenGeneral,
            'reporteFinanciero' => $reporteFinanciero,
            'reporteClientes' => $reporteClientes,
            'reporteProyectos' => $reporteProyectos,
            'proyectosPorEstado' => $proyectosPorEstado,
            'mesesFinanciero' => $mesesFinanciero,
            'ingresosMensuales' => $ingresosMensuales,
            'analisisIngresos' => $analisisIngresos
        ]);
    }
}
