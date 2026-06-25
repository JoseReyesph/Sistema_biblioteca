<?php
require_once __DIR__ . '/../data/ReporteDatos.php';

class ReporteNegocio {
    private $reporteDatos;

    public function __construct() {
        $this->reporteDatos = new ReporteDatos();
    }

    public function obtenerPrestamosDevueltos() {
        return $this->reporteDatos->obtenerPrestamosDevueltos();
    }

    public function obtenerPrestamosPendientes() {
        return $this->reporteDatos->obtenerPrestamosPendientes();
    }

    public function obtenerMultas() {
        return $this->reporteDatos->obtenerMultas();
    }
}