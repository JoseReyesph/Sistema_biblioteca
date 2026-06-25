<?php
require_once __DIR__ . '/../data/GestionDatos.php';

class GestionNegocio {
    private $datos;

    public function __construct() {
        $this->datos = new GestionDatos();
    }

    public function listarCategorias() { return $this->datos->listarCategorias(); }
    public function listarEditoriales() { return $this->datos->listarEditoriales(); }
    public function listarAutores() { return $this->datos->listarAutores(); }

    public function obtenerPorId($tabla, $id) {
        if ($tabla === 'categoria') return $this->datos->obtenerCategoriaPorId($id);
        if ($tabla === 'editorial') return $this->datos->obtenerEditorialPorId($id);
        if ($tabla === 'autor') return $this->datos->obtenerAutorPorId($id);
        return null;
    }

    public function procesarFormulario($post) {
        $tipoForm = $post['tipo_formulario'] ?? '';
        $id = isset($post['id']) ? (int)$post['id'] : 0;

        if ($tipoForm === 'categoria') {
            $nombre = trim($post['nombre_categoria'] ?? '');
            $desc = trim($post['descripcion'] ?? '');
            if ($nombre === '') return "El nombre de la categoría es obligatorio.";
            return $this->datos->guardarCategoria($id, $nombre, $desc) ? true : "Error al guardar categoría.";
        }

        if ($tipoForm === 'editorial') {
            $nombre = trim($post['nombre'] ?? '');
            $pais = trim($post['pais'] ?? '');
            $email = trim($post['email'] ?? '');
            $telefono = trim($post['telefono'] ?? '');
            $estado = $post['estado'] ?? 'activo';
            if ($nombre === '' || $pais === '' || $email === '' || $telefono === '') return "Todos los campos de la editorial son obligatorios.";
            return $this->datos->guardarEditorial($id, $nombre, $pais, $email, $telefono, $estado) ? true : "Error al guardar editorial.";
        }

        if ($tipoForm === 'autor') {
            $alias = trim($post['alias'] ?? '');
            $nombre = trim($post['nombre_completo'] ?? '');
            $nacionalidad = trim($post['nacionalidad'] ?? '');
            $fecha_nac = trim($post['fecha_nacimiento'] ?? '');
            $estado = $post['estado'] ?? 'activo';
            if ($alias === '' || $nombre === '' || $nacionalidad === '' || $fecha_nac === '') return "Todos los campos del autor son obligatorios.";
            return $this->datos->guardarAutor($id, $alias, $nombre, $nacionalidad, $fecha_nac, $estado) ? true : "Error al guardar autor.";
        }

        return "Formulario no válido.";
    }
}