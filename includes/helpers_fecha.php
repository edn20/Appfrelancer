<?php

function formatearFechaGlobal($fecha, $formato = null)
{
    if (!$fecha) {
        return 'Sin fecha';
    }

    $timestamp = strtotime($fecha);

    if (!$timestamp) {
        return 'Sin fecha';
    }

    $formato = $formato ?? ($_SESSION['formato_fecha'] ?? 'dd_mm_yyyy');

    $dias = [
        'Monday' => 'Lunes',
        'Tuesday' => 'Martes',
        'Wednesday' => 'Miércoles',
        'Thursday' => 'Jueves',
        'Friday' => 'Viernes',
        'Saturday' => 'Sábado',
        'Sunday' => 'Domingo'
    ];

    $meses = [
        'January' => 'Enero',
        'February' => 'Febrero',
        'March' => 'Marzo',
        'April' => 'Abril',
        'May' => 'Mayo',
        'June' => 'Junio',
        'July' => 'Julio',
        'August' => 'Agosto',
        'September' => 'Septiembre',
        'October' => 'Octubre',
        'November' => 'Noviembre',
        'December' => 'Diciembre'
    ];

    $dia = date('d', $timestamp);
    $mesNumero = date('m', $timestamp);
    $anio = date('Y', $timestamp);
    $anioCorto = date('y', $timestamp);

    $nombreDia = $dias[date('l', $timestamp)] ?? date('l', $timestamp);
    $nombreMes = $meses[date('F', $timestamp)] ?? date('F', $timestamp);

    switch ($formato) {
        case 'dd_mes_yyyy':
            return "{$dia} {$nombreMes} {$anio}";

        case 'dia_dd_mes_yyyy':
            return "{$nombreDia}, {$dia} de {$nombreMes} del {$anio}";

        case 'dd_mm_yy':
            return "{$dia}/{$mesNumero}/{$anioCorto}";

        case 'mes_dd_yyyy':
            return "{$nombreMes}, {$dia} del {$anio}";

        case 'dd_mm_yyyy':
        default:
            return "{$dia}/{$mesNumero}/{$anio}";
    }
}
