<?php
require_once __DIR__ . '/../data/MultaDatos.php';

class MultaNegocio {
    private $multaDatos;

    public function __construct() {
        $this->multaDatos = new MultaDatos();
    }

    public function listarPrestamosActivos() {
        return $this->multaDatos->listarPrestamosActivos();
    }

    public function procesarAccion($post) {
        $accion = $post['accion_multa'] ?? '';
        $prestamo_id = intval($post['prestamo_id'] ?? 0);
        $calificacion = intval($post['calificacion'] ?? 10); 
        $fecha_hoy = date('Y-m-d');

        if ($prestamo_id <= 0) return "ID de préstamo no válido.";
        if ($calificacion < 1 || $calificacion > 10) return "La calificación debe estar entre 1 y 10.";

        if ($accion === 'recibir_normal') {
            $ok = $this->multaDatos->recibirLibro($prestamo_id, $fecha_hoy, $calificacion);
            return $ok ? true : "Error al registrar la devolución del libro.";
        }

        if ($accion === 'aplicar_multa') {
            $dias = intval($post['dias_retraso'] ?? 0);
            if ($dias <= 0) {
                return "Los días de retraso deben ser mayores a 0.";
            }

            $monto = $dias * 1.00; 
            $motivo = "Retraso de " . $dias . " día(s) en la entrega del libro.";

            $ok = $this->multaDatos->aplicarMulta($prestamo_id, $monto, $motivo, $fecha_hoy, $calificacion);
            return $ok ? true : "Error al procesar la multa en el sistema.";
        }

        return false;
    }
}