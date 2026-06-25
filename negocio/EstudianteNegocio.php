<?php
require_once __DIR__ . '/../data/EstudianteDatos.php';

class EstudianteNegocio {
    private $estudianteDatos;

    public function __construct() {
        $this->estudianteDatos = new EstudianteDatos();
    }

    public function listarEstudiantes($busqueda = '') {
        return $this->estudianteDatos->listarEstudiantes(trim($busqueda));
    }

    public function obtenerEstudiantePorId($id) {
        return $this->estudianteDatos->obtenerEstudiantePorId((int)$id);
    }

    public function guardarEstudiante($datos) {
        $id = isset($datos['id']) ? (int)$datos['id'] : 0;
        $carnet = trim($datos['carnet_estudiante'] ?? '');
        $nombre = trim($datos['nombre'] ?? '');
        $telefono = trim($datos['telefono'] ?? '');
        $email = trim($datos['email'] ?? '');

        if ($carnet === '' || $nombre === '' || $telefono === '' || $email === '') {
            return "Todos los campos son obligatorios.";
        }

        if ($this->estudianteDatos->existeCarnet($carnet, $id)) {
            return "El carnet de estudiante ya está registrado.";
        }

        if ($id > 0) {
            $resultado = $this->estudianteDatos->actualizarEstudiante($id, $carnet, $nombre, $telefono, $email);
        } else {
            $resultado = $this->estudianteDatos->crearEstudiante($carnet, $nombre, $telefono, $email);
        }

        if (!$resultado) {
            return "No se pudo guardar el estudiante.";
        }

        return true;
    }

    public function eliminarEstudiante($id) {
        $id = (int)$id;
        if ($id <= 0) return "ID no válido.";

        if (!$this->estudianteDatos->eliminarEstudiante($id)) {
            return "No se pudo eliminar el estudiante.";
        }

        return true;
    }
}