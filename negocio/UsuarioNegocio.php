<?php

require_once __DIR__ . '/../data/UsuarioDatos.php';

class UsuarioNegocio {
    private $usuarioDatos;

    public function __construct() {
        $this->usuarioDatos = new UsuarioDatos();
    }

    public function loguear($username, $password) {
        $username = trim($username);
        $password = trim($password);

        if (empty($username) || empty($password)) {
            return "Por favor, llena todos los campos.";
        }

        $usuario = $this->usuarioDatos->obtenerUsuarioPorUsername($username);

        if (!$usuario) {
            return "El usuario no esta registrado";
        }

        if ($password !== $usuario['password']) {
            return "Credenciales incorrectas.";
        }

        $_SESSION['usuario_id']  = $usuario['id'];
        $_SESSION['usuario_nom'] = $usuario['user_name'];
        $_SESSION['usuario_rol'] = $usuario['rol'];

        return true;
    }

    public function listarUsuarios($busqueda = '') {
        return $this->usuarioDatos->listarUsuarios(trim($busqueda));
    }

    public function obtenerUsuarioPorId($id) {
        return $this->usuarioDatos->obtenerUsuarioPorId((int) $id);
    }

    /*public function guardarUsuario($datos) {
        $id = isset($datos['id']) ? (int) $datos['id'] : 0;
        $username = trim($datos['user_name'] ?? '');
        $email = trim($datos['email'] ?? '');
        $password = trim($datos['password'] ?? '');
        $rol = $datos['rol'] ?? '';
        $estado = $datos['estado'] ?? 'activo';

        $rolesPermitidos = ['administrador', 'gestion', 'usuario'];
        $estadosPermitidos = ['activo', 'inactivo'];

        if ($username === '' || $email === '' || $rol === '') {
            return "Completa los campos obligatorios.";
        }

        if (!in_array($rol, $rolesPermitidos)) {
            return "Selecciona un rol valido.";
        }

        if (!in_array($estado, $estadosPermitidos)) {
            return "Selecciona un estado valido.";
        }

        if ($id === 0 && $password === '') {
            return "La contrasena es obligatoria para crear usuarios.";
        }

        if ($this->usuarioDatos->existeUsername($username, $id)) {
            return "El nombre de usuario ya existe.";
        }

        if ($id > 0) {
            $resultado = $this->usuarioDatos->actualizarUsuario($id, $username, $email, $rol, $estado, $password);
        } else {
            $resultado = $this->usuarioDatos->crearUsuario($username, $email, $password, $rol);
        }

        if (!$resultado) {
            return "No se pudo guardar el usuario.";
        }

        return true;
    }*/
    public function guardarUsuario($datos) {
    $id = isset($datos['id']) ? (int) $datos['id'] : 0;
    $username = trim($datos['user_name'] ?? '');
    $email = trim($datos['email'] ?? '');
    $password = trim($datos['password'] ?? '');
    $rol = $datos['rol'] ?? 'usuario';
    $estado = $datos['estado'] ?? 'activo';

    // Validación súper básica
    if ($username === '' || $email === '' || $rol === '') {
        return "Completa los campos obligatorios.";
    }

    if ($id === 0 && $password === '') {
        return "La contrasena es obligatoria para crear usuarios.";
    }

    // Verificar si existe (mantiene el flujo hacia la capa Datos)
    if ($this->usuarioDatos->existeUsername($username, $id)) {
        return "El nombre de usuario ya existe.";
    }

    // Insertar o Actualizar directo
    if ($id > 0) {
        $resultado = $this->usuarioDatos->actualizarUsuario($id, $username, $email, $rol, $estado, $password);
    } else {
        $resultado = $this->usuarioDatos->crearUsuario($username, $email, $password, $rol);
    }

    if ($resultado == false) {
        return "No se pudo guardar el usuario.";
    }

    return true;
}

    public function desactivarUsuario($id) {
        $id = (int) $id;

        if ($id <= 0) {
            return "Usuario no valido.";
        }

        if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $id) {
            return "No puedes desactivar tu propio usuario.";
        }

        if (!$this->usuarioDatos->desactivarUsuario($id)) {
            return "No se pudo desactivar el usuario.";
        }

        return true;
    }
}
