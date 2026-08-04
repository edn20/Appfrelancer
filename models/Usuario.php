<?php

namespace Model;

class Usuario extends ActiveRecord
{
    protected static $tabla = 'usuarios';

    protected static $columnasDB = [
        'id',
        'nombre',
        'apellido',
        'email',
        'avatar',
        'password',
        'token',
        'confirmado',
        'rol_id',
        'estado'
    ];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $avatar;
    public $password;
    public $password2;
    public $token;
    public $confirmado;
    public $rol_id;
    public $estado;

    public $creado;
    public $actualizado;

    public $password_actual;
    public $password_nuevo;

    public $rol_nombre;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
        $this->rol_id = $args['rol_id'] ?? 1;
        $this->estado = $args['estado'] ?? 0;

        $this->creado = $args['creado'] ?? null;
        $this->actualizado = $args['actualizado'] ?? null;
    }

    // Validar el Login de Usuarios
    public function validarLogin()
    {
        if (!$this->email) {
            self::$alertas['error'][] = 'El Email del Usuario es Obligatorio';
        }
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no válido';
        }
        if (!$this->password) {
            self::$alertas['error'][] = 'El Password no puede ir vacio';
        }
        return self::$alertas;
    }

    // Validación para cuentas nuevas
    public function validar_cuenta()
    {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El Nombre es Obligatorio';
        }
        if (!$this->apellido) {
            self::$alertas['error'][] = 'El Apellido es Obligatorio';
        }
        if (!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }
        if (!$this->password) {
            self::$alertas['error'][] = 'El Password no puede ir vacio';
        }
        if (strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres';
        }
        if ($this->password !== $this->password2) {
            self::$alertas['error'][] = 'Los password son diferentes';
        }
        return self::$alertas;
    }

    // Valida un email
    public function validarEmail()
    {
        if (!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no válido';
        }
        return self::$alertas;
    }

    // Valida el Password 
    public function validarPassword()
    {
        if (!$this->password) {
            self::$alertas['error'][] = 'El Password no puede ir vacio';
        }
        if (strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres';
        }
        return self::$alertas;
    }

    public function nuevo_password(): array
    {
        if (!$this->password_actual) {
            self::$alertas['error'][] = 'El Password Actual no puede ir vacio';
        }
        if (!$this->password_nuevo) {
            self::$alertas['error'][] = 'El Password Nuevo no puede ir vacio';
        }
        if (strlen($this->password_nuevo) < 6) {
            self::$alertas['error'][] = 'El Password debe contener al menos 6 caracteres';
        }
        return self::$alertas;
    }

    // Comprobar el password
    public function comprobar_password(): bool
    {
        return password_verify($this->password_actual, $this->password);
    }

    // Hashea el password
    public function hashPassword(): void
    {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    // Generar un Token
    public function crearToken(): void
    {
        $this->token = uniqid();
    }

    public static function visiblesParaAdmin($filtros = [])
    {
        $query = "SELECT 
                u.*,
                r.nombre AS rol_nombre
              FROM usuarios u
              LEFT JOIN roles r ON u.rol_id = r.id
              WHERE 1=1";

        if (!empty($filtros['busqueda'])) {
            $busqueda = self::$db->escape_string($filtros['busqueda']);

            $query .= " AND (
            u.nombre LIKE '%{$busqueda}%'
            OR u.apellido LIKE '%{$busqueda}%'
            OR u.email LIKE '%{$busqueda}%'
        )";
        }

        if (isset($filtros['estado']) && $filtros['estado'] !== '') {
            $estado = self::$db->escape_string($filtros['estado']);
            $query .= " AND u.estado = '{$estado}'";
        }

        if (isset($filtros['confirmado']) && $filtros['confirmado'] !== '') {
            $confirmado = self::$db->escape_string($filtros['confirmado']);
            $query .= " AND u.confirmado = '{$confirmado}'";
        }

        if (!empty($filtros['rol_id'])) {
            $rolId = self::$db->escape_string($filtros['rol_id']);
            $query .= " AND u.rol_id = '{$rolId}'";
        }

        if (!empty($filtros['pendientes'])) {
            $query .= " AND u.confirmado = 1 AND u.estado = 0";
        }

        $query .= " ORDER BY 
                    CASE 
                        WHEN u.confirmado = 1 AND u.estado = 0 THEN 1
                        WHEN u.estado = 1 THEN 2
                        ELSE 3
                    END,
                    u.id DESC";

        return self::consultarSQL($query);
    }

    public static function resumenUsuarios()
    {
        $query = "SELECT
                COUNT(id) AS total,
                SUM(CASE WHEN confirmado = 1 AND estado = 0 THEN 1 ELSE 0 END) AS pendientes,
                SUM(CASE WHEN estado = 1 THEN 1 ELSE 0 END) AS activos,
                SUM(CASE WHEN rol_id = 2 AND estado = 1 THEN 1 ELSE 0 END) AS freelancers,
                SUM(CASE WHEN rol_id = 3 AND estado = 1 THEN 1 ELSE 0 END) AS administradores
              FROM usuarios";

        $resultado = self::$db->query($query);
        return $resultado->fetch_assoc();
    }

    public static function totalAdministradoresActivos()
    {
        $query = "SELECT COUNT(id) AS total 
              FROM usuarios 
              WHERE rol_id = 3 
              AND estado = 1";

        $resultado = self::$db->query($query);
        $registro = $resultado->fetch_assoc();

        return (int) ($registro['total'] ?? 0);
    }

    public static function totalPendientesAlta()
    {
        $query = "SELECT COUNT(*) AS total
              FROM " . static::$tabla . "
              WHERE confirmado = 1
              AND estado = 0";

        $resultado = self::$db->query($query);

        if (!$resultado) {
            return 0;
        }

        $row = $resultado->fetch_assoc();

        return (int) ($row['total'] ?? 0);
    }
}
