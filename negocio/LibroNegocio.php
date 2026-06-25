<?php
require_once __DIR__ . '/../data/LibroDatos.php';
require_once __DIR__ . '/../data/GestionDatos.php'; 

class LibroNegocio {
    private $datos;
    private $gestionDatos;

    public function __construct() {
        $this->datos = new LibroDatos();
        $this->gestionDatos = new GestionDatos();
    }

    public function listarLibros() { return $this->datos->listarLibros(); }
    public function obtenerLibroPorId($id) { return $this->datos->obtenerLibroPorId((int)$id); }
    

    public function buscarAutores() { return $this->gestionDatos->listarAutores(); }
    public function buscarEditoriales() { return $this->gestionDatos->listarEditoriales(); }
    public function buscarCategorias() { return $this->gestionDatos->listarCategorias(); }

    public function guardarLibro($post) {
        $id = isset($post['id']) ? (int)$post['id'] : 0;
        $titulo = trim($post['titulo'] ?? '');
        $autor_id = (int)($post['autor_id'] ?? 0);
        $editorial_id = (int)($post['editorial_id'] ?? 0);
        $anio = trim($post['anio_publicacion'] ?? '');
        $idioma = trim($post['idioma'] ?? '');
        $desc = trim($post['descripcion'] ?? '');
        $cat_p = (int)($post['cat_principal'] ?? 0);
        $cat_s = (int)($post['cat_secundaria'] ?? 0);

        if ($titulo === '' || $autor_id === 0 || $editorial_id === 0 || $cat_p === 0) {
            return "El título, autor, editorial y categoría principal son requeridos.";
        }
        return $this->datos->guardarLibro($id, $titulo, $autor_id, $editorial_id, $anio, $idioma, $desc, $cat_p, $cat_s) ? true : "Error al guardar el libro.";
    }

    
    public function listarCopias($libro_id) { return $this->datos->listarCopiasPorLibro((int)$libro_id); }
    
    public function guardarCopia($post) {
        $libro_id = (int)($post['libro_id'] ?? 0);
        $estado = $post['estado'] ?? 'disponible';
        $fecha_adq = trim($post['fecha_adquisicion'] ?? '');
        $ubicacion = trim($post['ubicacion'] ?? '');

        if ($libro_id <= 0 || $fecha_adq === '') return "La fecha de adquisición es obligatoria.";
        
        $estadosValidos = ['disponible', 'prestado', 'dañado', 'reservado'];
        if (!in_array($estado, $estadosValidos)) return "Estado de copia no válido.";

        return $this->datos->guardarCopia($libro_id, $estado, $fecha_adq, $ubicacion) ? true : "Error al registrar copia.";
    }
}