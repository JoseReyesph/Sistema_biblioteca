<?php
require_once __DIR__ . '/../data/PrestamoDatos.php';

class PrestamoNegocio {

    private $prestamoDatos;

    public function __construct() {
        $this->prestamoDatos = new PrestamoDatos();
    }

    public function obtenerEstudiantes() { return $this->prestamoDatos->obtenerEstudiantes(); }
    public function buscarLibros($buscar = '') { return $this->prestamoDatos->buscarLibros(trim($buscar)); }
    public function listarCopiasPorLibro($libro_id) { return $this->prestamoDatos->listarCopiasPorLibro((int)$libro_id); }
    public function obtenerDetalleCopia($copia_id) { return $this->prestamoDatos->obtenerDetalleCopia((int)$copia_id); }

    public function procesarPrestamo($post, &$carrito) {
        $accion = $post['accion_prestamo'] ?? '';

        if ($accion === 'agregar_item') {
            $copia_id = intval($post['copia_id'] ?? 0);
            if ($copia_id > 0 && !in_array($copia_id, $carrito)) {
                $carrito[] = $copia_id;
            }
            return true;
        }

        if ($accion === 'guardar_prestamo') {
            $estudiante_id = intval($post['estudiante_id'] ?? 0);
            $fecha_prestamo = $post['fecha_prestamo'] ?? '';
            $fecha_limite = $post['fecha_limite'] ?? '';
            
            $user_id = $_SESSION['usuario_id'] ?? 1; 

            if ($estudiante_id <= 0 || empty($fecha_prestamo) || empty($fecha_limite)) {
                return "Por favor, seleccione el estudiante y complete las fechas.";
            }

            if (empty($carrito)) {
                return "El carrito de préstamos está vacío. Agregue al menos una copia.";
            }

            if ($fecha_prestamo > $fecha_limite) {
                return "La fecha de préstamo no puede ser mayor que la fecha límite.";
            }

            $resultado = $this->prestamoDatos->registrarPrestamo($estudiante_id, $user_id, $fecha_prestamo, $fecha_limite, $carrito);
            
            if ($resultado) {
                $carrito = [];
                return true;
            } else {
                return "Error al registrar el préstamo en la base de datos.";
            }
        }

        return 'formulario no valido';
    }
}