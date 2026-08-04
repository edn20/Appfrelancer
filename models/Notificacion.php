<?php

namespace Model;

use Model\ConfiguracionNotificacion;

class Notificacion extends ActiveRecord
{
    protected static $tabla = '';
    protected static $columnasDB = [];

    public $tipo;
    public $grupo;
    public $titulo;
    public $mensaje;
    public $url;
    public $icono;
    public $nivel;
    public $fecha;
    public $dias;

    public function __construct($args = [])
    {
        $this->tipo = $args['tipo'] ?? '';
        $this->grupo = $args['grupo'] ?? '';
        $this->titulo = $args['titulo'] ?? '';
        $this->mensaje = $args['mensaje'] ?? '';
        $this->url = $args['url'] ?? '';
        $this->icono = $args['icono'] ?? 'bi-info-circle';
        $this->nivel = $args['nivel'] ?? 'info';
        $this->fecha = $args['fecha'] ?? '';
        $this->dias = $args['dias'] ?? null;
    }

    public static function obtenerPorUsuario($usuarioId, $limite = null)
    {
        $configuracion = ConfiguracionNotificacion::porUsuario($usuarioId);

        $notificaciones = [];

        if ($configuracion->estaActiva('tareas_vencidas') || $configuracion->estaActiva('tareas_hoy') || $configuracion->estaActiva('tareas_proximas')) {
            $notificaciones = array_merge($notificaciones, self::tareasPorVencer($usuarioId, $configuracion));
        }

        if ($configuracion->estaActiva('pagos_vencidos')) {
            $notificaciones = array_merge($notificaciones, self::pagosVencidos($usuarioId));
        }

        if ($configuracion->estaActiva('pagos_proximos')) {
            $notificaciones = array_merge($notificaciones, self::pagosProximos($usuarioId));
        }

        if ($configuracion->estaActiva('proyectos_atrasados')) {
            $notificaciones = array_merge($notificaciones, self::proyectosAtrasados($usuarioId));
        }

        if ($configuracion->estaActiva('proyectos_proximos')) {
            $notificaciones = array_merge($notificaciones, self::proyectosProximos($usuarioId));
        }

        usort($notificaciones, function ($a, $b) {
            return strtotime($a->fecha) <=> strtotime($b->fecha);
        });

        if ($limite) {
            return array_slice($notificaciones, 0, $limite);
        }

        return $notificaciones;
    }

    public static function obtenerTopbarPorUsuario($usuarioId)
    {
        $notificaciones = self::obtenerPorUsuario($usuarioId);

        $gruposPermitidos = [
            'tarea_vencida',
            'tarea_hoy',
            'tarea_proxima',
            'pago_vencido',
            'pago_proximo',
            'proyecto_atrasado',
            'proyecto_proximo'
        ];

        $notificacionesTopbar = [];

        foreach ($notificaciones as $notificacion) {
            if (!in_array($notificacion->grupo, $gruposPermitidos)) {
                continue;
            }

            if (!isset($notificacionesTopbar[$notificacion->grupo])) {
                $notificacionesTopbar[$notificacion->grupo] = $notificacion;
            }
        }

        return array_values($notificacionesTopbar);
    }

    public static function totalPorUsuario($usuarioId)
    {
        return count(self::obtenerPorUsuario($usuarioId));
    }

    public static function resumenPorUsuario($usuarioId)
    {
        $notificaciones = self::obtenerPorUsuario($usuarioId);

        $resumen = [
            'total' => 0,

            'tareas' => 0,
            'tareas_vencidas' => 0,
            'tareas_proximas' => 0,

            'pagos' => 0,
            'pagos_vencidos' => 0,
            'pagos_proximos' => 0,

            'proyectos' => 0,
            'proyectos_atrasados' => 0,
            'proyectos_proximos' => 0,

            'criticas' => 0
        ];

        foreach ($notificaciones as $notificacion) {
            $resumen['total']++;

            if ($notificacion->tipo === 'tarea') {
                $resumen['tareas']++;

                if ($notificacion->grupo === 'tarea_vencida') {
                    $resumen['tareas_vencidas']++;
                }

                if ($notificacion->grupo === 'tarea_hoy' || $notificacion->grupo === 'tarea_proxima') {
                    $resumen['tareas_proximas']++;
                }
            }

            if ($notificacion->tipo === 'pago') {
                $resumen['pagos']++;

                if ($notificacion->grupo === 'pago_vencido') {
                    $resumen['pagos_vencidos']++;
                }

                if ($notificacion->grupo === 'pago_proximo') {
                    $resumen['pagos_proximos']++;
                }
            }

            if ($notificacion->tipo === 'proyecto') {
                $resumen['proyectos']++;

                if ($notificacion->grupo === 'proyecto_atrasado') {
                    $resumen['proyectos_atrasados']++;
                }

                if ($notificacion->grupo === 'proyecto_proximo') {
                    $resumen['proyectos_proximos']++;
                }
            }

            if ($notificacion->nivel === 'danger') {
                $resumen['criticas']++;
            }
        }

        return $resumen;
    }

    private static function tareasPorVencer($usuarioId, $configuracion)
    {
        $usuarioId = self::$db->escape_string($usuarioId);
        $hoy = date('Y-m-d');
        $limite = date('Y-m-d', strtotime('+5 days'));

        $query = "SELECT 
                    t.id,
                    t.nombre,
                    t.fecha_limite,
                    t.estado,
                    p.nombre AS proyecto_nombre,
                    c.nombre AS cliente_nombre,
                    c.apellido AS cliente_apellido,
                    c.empresa AS cliente_empresa,
                    DATEDIFF(t.fecha_limite, '{$hoy}') AS dias
                  FROM tareas t
                  INNER JOIN proyectos p ON t.proyecto_id = p.id
                  INNER JOIN clientes c ON p.cliente_id = c.id
                  WHERE t.usuario_id = '{$usuarioId}'
                  AND t.eliminado = 0
                  AND p.eliminado = 0
                  AND c.eliminado = 0
                  AND t.fecha_limite IS NOT NULL
                  AND t.fecha_limite <= '{$limite}'
                  AND t.estado NOT IN ('Completada', 'Anulada')
                  ORDER BY t.fecha_limite ASC";

        $resultado = self::$db->query($query);

        $notificaciones = [];

        while ($row = $resultado->fetch_assoc()) {
            $dias = (int) $row['dias'];

            if ($dias < 0) {
                $grupo = 'tarea_vencida';
                $titulo = 'Tarea vencida';
                $mensaje = 'La tarea "' . $row['nombre'] . '" venció hace ' . abs($dias) . ' día(s).';
                $nivel = 'danger';
            } elseif ($dias === 0) {
                $grupo = 'tarea_hoy';
                $titulo = 'Tarea vence hoy';
                $mensaje = 'La tarea "' . $row['nombre'] . '" vence hoy.';
                $nivel = 'warning';
            } else {
                $grupo = 'tarea_proxima';
                $titulo = 'Tarea próxima a vencer';
                $mensaje = 'La tarea "' . $row['nombre'] . '" vence en ' . $dias . ' día(s).';
                $nivel = 'warning';
            }

            if ($grupo === 'tarea_vencida' && !$configuracion->estaActiva('tareas_vencidas')) {
                continue;
            }

            if ($grupo === 'tarea_hoy' && !$configuracion->estaActiva('tareas_hoy')) {
                continue;
            }

            if ($grupo === 'tarea_proxima' && !$configuracion->estaActiva('tareas_proximas')) {
                continue;
            }

            $notificaciones[] = new self([
                'tipo' => 'tarea',
                'grupo' => $grupo,
                'titulo' => $titulo,
                'mensaje' => $mensaje,
                'url' => '/tareas/detalle?id=' . $row['id'],
                'icono' => 'bi-check2-square',
                'nivel' => $nivel,
                'fecha' => $row['fecha_limite'],
                'dias' => $dias
            ]);
        }

        return $notificaciones;
    }

    private static function pagosVencidos($usuarioId)
    {
        $usuarioId = self::$db->escape_string($usuarioId);
        $hoy = date('Y-m-d');

        $query = "SELECT 
                    pg.id,
                    pg.fecha_vencimiento,
                    pg.monto_total,
                    pg.monto_pagado,
                    pg.estado,
                    p.nombre AS proyecto_nombre,
                    c.nombre AS cliente_nombre,
                    c.apellido AS cliente_apellido,
                    c.empresa AS cliente_empresa,
                    DATEDIFF('{$hoy}', pg.fecha_vencimiento) AS dias
                  FROM pagos pg
                  INNER JOIN proyectos p ON pg.proyecto_id = p.id
                  INNER JOIN clientes c ON p.cliente_id = c.id
                  WHERE pg.usuario_id = '{$usuarioId}'
                  AND pg.eliminado = 0
                  AND p.eliminado = 0
                  AND c.eliminado = 0
                  AND pg.fecha_vencimiento IS NOT NULL
                  AND pg.fecha_vencimiento < '{$hoy}'
                  AND pg.estado IN ('Pendiente', 'Por confirmar')
                  ORDER BY pg.fecha_vencimiento ASC";

        $resultado = self::$db->query($query);

        $notificaciones = [];

        while ($row = $resultado->fetch_assoc()) {
            $cliente = trim(($row['cliente_nombre'] ?? '') . ' ' . ($row['cliente_apellido'] ?? ''));

            if (!empty($row['cliente_empresa'])) {
                $cliente .= $cliente ? ' - ' . $row['cliente_empresa'] : $row['cliente_empresa'];
            }

            $notificaciones[] = new self([
                'tipo' => 'pago',
                'grupo' => 'pago_vencido',
                'titulo' => 'Pago vencido',
                'mensaje' => 'El pago de ' . $cliente . ' está vencido hace ' . (int) $row['dias'] . ' día(s).',
                'url' => '/pagos/detalle?id=' . $row['id'],
                'icono' => 'bi-cash-coin',
                'nivel' => 'danger',
                'fecha' => $row['fecha_vencimiento'],
                'dias' => (int) $row['dias']
            ]);
        }

        return $notificaciones;
    }

    private static function proyectosAtrasados($usuarioId)
    {
        $usuarioId = self::$db->escape_string($usuarioId);
        $hoy = date('Y-m-d');

        $query = "SELECT 
                    p.id,
                    p.nombre,
                    p.fecha_entrega,
                    p.estado,
                    c.nombre AS cliente_nombre,
                    c.apellido AS cliente_apellido,
                    c.empresa AS cliente_empresa,
                    DATEDIFF('{$hoy}', p.fecha_entrega) AS dias
                  FROM proyectos p
                  INNER JOIN clientes c ON p.cliente_id = c.id
                  WHERE p.usuario_id = '{$usuarioId}'
                  AND p.eliminado = 0
                  AND c.eliminado = 0
                  AND p.fecha_entrega IS NOT NULL
                  AND p.fecha_entrega < '{$hoy}'
                  AND p.estado NOT IN ('Entregado', 'Cancelado')
                  ORDER BY p.fecha_entrega ASC";

        $resultado = self::$db->query($query);

        $notificaciones = [];

        while ($row = $resultado->fetch_assoc()) {
            $notificaciones[] = new self([
                'tipo' => 'proyecto',
                'grupo' => 'proyecto_atrasado',
                'titulo' => 'Proyecto atrasado',
                'mensaje' => 'El proyecto "' . $row['nombre'] . '" está atrasado hace ' . (int) $row['dias'] . ' día(s).',
                'url' => '/proyectos/detalle?id=' . $row['id'],
                'icono' => 'bi-kanban',
                'nivel' => 'danger',
                'fecha' => $row['fecha_entrega'],
                'dias' => (int) $row['dias']
            ]);
        }

        return $notificaciones;
    }

    private static function pagosProximos($usuarioId)
    {
        $usuarioId = self::$db->escape_string($usuarioId);
        $hoy = date('Y-m-d');
        $limite = date('Y-m-d', strtotime('+5 days'));

        $query = "SELECT 
                pg.id,
                pg.fecha_vencimiento,
                pg.monto_total,
                pg.estado,
                p.nombre AS proyecto_nombre,
                c.nombre AS cliente_nombre,
                c.apellido AS cliente_apellido,
                c.empresa AS cliente_empresa,
                DATEDIFF(pg.fecha_vencimiento, '{$hoy}') AS dias
              FROM pagos pg
              INNER JOIN proyectos p ON pg.proyecto_id = p.id
              INNER JOIN clientes c ON p.cliente_id = c.id
              WHERE pg.usuario_id = '{$usuarioId}'
              AND pg.eliminado = 0
              AND p.eliminado = 0
              AND c.eliminado = 0
              AND pg.fecha_vencimiento IS NOT NULL
              AND pg.fecha_vencimiento >= '{$hoy}'
              AND pg.fecha_vencimiento <= '{$limite}'
              AND pg.estado IN ('Pendiente', 'Por confirmar')
              ORDER BY pg.fecha_vencimiento ASC";

        $resultado = self::$db->query($query);

        $notificaciones = [];

        while ($row = $resultado->fetch_assoc()) {
            $cliente = trim(($row['cliente_nombre'] ?? '') . ' ' . ($row['cliente_apellido'] ?? ''));

            if (!empty($row['cliente_empresa'])) {
                $cliente .= $cliente ? ' - ' . $row['cliente_empresa'] : $row['cliente_empresa'];
            }

            $dias = (int) $row['dias'];

            $titulo = $dias === 0 ? 'Pago vence hoy' : 'Pago próximo a vencer';
            $mensaje = $dias === 0
                ? 'El pago de ' . $cliente . ' vence hoy.'
                : 'El pago de ' . $cliente . ' vence en ' . $dias . ' día(s).';

            $notificaciones[] = new self([
                'tipo' => 'pago',
                'grupo' => 'pago_proximo',
                'titulo' => $titulo,
                'mensaje' => $mensaje,
                'url' => '/pagos/detalle?id=' . $row['id'],
                'icono' => 'bi-cash',
                'nivel' => 'warning',
                'fecha' => $row['fecha_vencimiento'],
                'dias' => $dias
            ]);
        }

        return $notificaciones;
    }

    private static function proyectosProximos($usuarioId)
    {
        $usuarioId = self::$db->escape_string($usuarioId);
        $hoy = date('Y-m-d');
        $limite = date('Y-m-d', strtotime('+5 days'));

        $query = "SELECT 
                p.id,
                p.nombre,
                p.fecha_entrega,
                p.estado,
                c.nombre AS cliente_nombre,
                c.apellido AS cliente_apellido,
                c.empresa AS cliente_empresa,
                DATEDIFF(p.fecha_entrega, '{$hoy}') AS dias
              FROM proyectos p
              INNER JOIN clientes c ON p.cliente_id = c.id
              WHERE p.usuario_id = '{$usuarioId}'
              AND p.eliminado = 0
              AND c.eliminado = 0
              AND p.fecha_entrega IS NOT NULL
              AND p.fecha_entrega >= '{$hoy}'
              AND p.fecha_entrega <= '{$limite}'
              AND p.estado NOT IN ('Entregado', 'Cancelado')
              ORDER BY p.fecha_entrega ASC";

        $resultado = self::$db->query($query);

        $notificaciones = [];

        while ($row = $resultado->fetch_assoc()) {
            $dias = (int) $row['dias'];

            $titulo = $dias === 0 ? 'Proyecto vence hoy' : 'Proyecto próximo a entregar';
            $mensaje = $dias === 0
                ? 'El proyecto "' . $row['nombre'] . '" debe entregarse hoy.'
                : 'El proyecto "' . $row['nombre'] . '" debe entregarse en ' . $dias . ' día(s).';

            $notificaciones[] = new self([
                'tipo' => 'proyecto',
                'grupo' => 'proyecto_proximo',
                'titulo' => $titulo,
                'mensaje' => $mensaje,
                'url' => '/proyectos/detalle?id=' . $row['id'],
                'icono' => 'bi-calendar-event',
                'nivel' => 'warning',
                'fecha' => $row['fecha_entrega'],
                'dias' => $dias
            ]);
        }

        return $notificaciones;
    }
}
